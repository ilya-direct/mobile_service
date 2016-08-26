<?php
/**
 * @var array $flashArray
 */
?>
<h3>Цены успешно загружены! Следующие данные были изменены:</h3>
<table class="table table-condensed table-bordered">
    <thead>
    <tr>
        <th>Имя устройства</th>
        <th>Услуга</th>
        <th>Цена</th>
        <th>Старая цена</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($flashArray)): ?>
        <?php foreach($flashArray as $tableRow): ?>
        <tr>
            <td><?= $tableRow['device']; ?></td>
            <td><?= $tableRow['service']; ?></td>
            <td><?= $tableRow['price']; ?></td>
            <td><?= $tableRow['price_old']; ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">
                Ничего не было изменено
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
