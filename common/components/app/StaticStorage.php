<?php

namespace common\components\app;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use creocoder\flysystem\Filesystem;
use League\Flysystem\FileNotFoundException;

/**
 * Class StaticStorage
 * сохранение файлов на статик сервер
 */
class StaticStorage extends Component
{
    /**
     * @var array all sections (top folders)
     */
    protected $sections = [];
    
    /**
     * @var integer
     */
    protected $nestingLevel = 1;
    
    /**
     * @var Filesystem
     */
    public $filesystem;
    
    /**
     * @var string
     */
    public $defaultFolder = 'common';
    
    /**
     * @var string Публичная ссылка
     */
    public $baseUrl = '';
    
    
    public function __construct($config = [])
    {
        if (!empty($config['sections']) && is_array($config['sections'])) {
            $this->sections = $config['sections'];
        }
        
        if (isset($config['nestingLevel']) && is_int($config['nestingLevel'])) {
            $level = (int)$config['nestingLevel'];
            
            if ($level > 31 || $level < 0) {
                throw new InvalidConfigException('Nesting level can\'t be more then 31 and less then 0');
            }
            $this->nestingLevel = $level;
        }
        
        unset($config['sections'], $config['nestingLevel']);
        
        parent::__construct($config);
    }
    
    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        $this->filesystem = Yii::createObject($this->filesystem);
        if (!$this->filesystem instanceof Filesystem) {
            throw new InvalidConfigException('FileSystem must be inherited from FileSystem');
        }
        $this->sections[] = $this->defaultFolder;
    }
    
    /**
     * Сохранение файла
     *
     * @param $fileName string Имя файла
     * @param string $contents Содержимое
     * @param string $section
     *
     * @return bool
     */
    public function saveContents(string $fileName, string $contents, string $section = '')
    {
        $filePath = $this->buildPath($fileName, $section);
        $this->filesystem->put($filePath, $contents);
        
        return true;
    }
    
    /**
     * Сохранение файла
     *
     * @param string $fileName
     * @param resource $resource
     * @param string $section
     * @return bool
     */
    public function saveFile(string $fileName, $resource, string $section = '')
    {
        if (is_resource($resource) == false) {
            throw new InvalidParamException(sprintf('Argument must be a valid resource type. %s given.',
                gettype($resource)));
        }
        $filePath = $this->buildPath($fileName, $section);;
        $this->filesystem->putStream($filePath, $resource);
        
        return true;
    }
    
    /**
     * Сохранение файла из заданного файла
     *
     * @param string $filePath Путь к файлу, который нужно сохранить
     * @param string|bool $newFileName Новое имя для файла, false если оставить имя без изменения
     * @param string $section
     * @return bool
     * @throws Exception
     */
    public function saveFileByPath(string $filePath,  $newFileName = false, string $section = '')
    {
        $filePath = Yii::getAlias($filePath);
        if (!file_exists($filePath)) {
            throw new Exception('File '. $filePath .' does not exist!');
        }
        $fileName = $newFileName ?: basename($filePath);
        $fileContents = file_get_contents($filePath);
        
        return $this->saveContents($fileName, $fileContents, $section);
    }
    
    /**
     * Получение файла
     *
     * @param string $fileName
     * @param string $section
     * @return false|string
     */
    public function getContents(string $fileName, string $section = '')
    {
        $filePath = $this->buildPath($fileName, $section);
        
        return $this->filesystem->read($filePath);
        
    }
    
    /**
     * Генерация пути к файлу
     *
     * @param string $fileName
     * @param string $section
     * @return string
     * @throws \yii\base\Exception
     */
    protected function buildPath(string $fileName, string $section)
    {
        $fileName = trim($fileName);
        // Deleting directory separators in file name with
        $fileName = preg_replace('/\/|\\\/', '', $fileName);
        
        if (empty($fileName)) {
            throw new InvalidParamException('File name can\' be empty');
        }
        if (empty($section)) {
            $section = $this->defaultFolder;
        } elseif (!in_array($section, $this->sections)) {
            throw new InvalidParamException('Section ' . $section . ' is unknown');
        }
        $fileHash = hash('sha256', $fileName);
        
        $dirs = [$section];
        $i = $this->nestingLevel;
        while ($i > 0) {
            $dirs[] = substr($fileHash, 0, 2);
            $fileHash = substr($fileHash, 2);
            $i--;
        }
        
        $filePath = implode('/', $dirs) . '/' . $fileName;
        
        return $filePath;
    }
    
    /**
     * Проверка существования файла
     *
     * @param $fileName
     * @param string $section
     * @return bool
     */
    public function fileExists(string $fileName, string $section = '')
    {
        $filePath = $this->buildPath($fileName, $section);
        
        return $this->filesystem->has($filePath);
    }
    
    /**
     * Удаление файла
     *
     * @param $fileName
     * @param string $section
     * @param bool $throwIfNotExists
     * @return bool
     * @throws FileNotFoundException
     */
    public function delete(string $fileName, string $section = '', bool $throwIfNotExists = false)
    {
        $filePath = $this->buildPath($fileName, $section);
        $return = false;
        try {
            $return = $this->filesystem->delete($filePath);
        } catch (FileNotFoundException $e) {
            if ($throwIfNotExists) {
                throw new FileNotFoundException('File ' . $fileName . ' not found');
            }
        }
        
        return $return;
        
    }
    
    /**
     * Удаление нескольких файлов
     *
     * @param array $fileNames
     * @param string $section
     */
    public function deleteAll(array $fileNames, string $section = '')
    {
        foreach ($fileNames as $fileName) {
            $this->delete((string)$fileName, $section);
        }
    }
    
    /**
     * Получение ссылки на файл
     *
     * @param string|null $fileName
     * @param string $section
     * @return false|string  false, если файла не сушествует
     */
    public function getUrl($fileName, string $section = '')
    {
        if (!$fileName) {
            
            return false;
        }
    
        $baseUrl = Yii::getAlias($this->baseUrl);
        
        if ($this->fileExists((string)$fileName, $section)) {
            
            return $baseUrl . '/' . $this->buildPath($fileName, $section);
        }
        
        return false;
    }
    
    /**
     * Получение относительного пути к файлу, если он существует
     *
     * @param string $fileName
     * @param string $section
     * @return bool|string
     */
    public function getPath(string $fileName, string $section = '')
    {
        if ($this->fileExists($fileName, $section)) {
            return $this->buildPath($fileName, $section);
        }
        
        return false;
    }
}
