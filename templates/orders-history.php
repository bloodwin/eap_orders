<?
	/*Шаблон для отображения содержимого истории заказов пользователя*/
	/*Данный шаблон можно разместить в папке используемого шаблона /wp-content/wp-recall/templates/ и он будет подключаться оттуда*/
?>
<?php
    global $user_ID, $wpdb;
    $eap_orders = Eap_Orders_History::getHistoryByUserID($user_ID, $wpdb, EAP_PREF);
?>
<div class="order-data">
    <table>
        <tr>
            <th>Номер заказа</th>
            <th>Дата заказа</th>
            <th>Статус заказа</th>
        </tr>
        <?php foreach ($eap_orders as $eap_order) { ?>
            <tr>
                <td>
                    <a href="<?php echo rcl_format_url(get_author_posts_url($user_ID), 'eap_orders'); ?>&order-id=<?php echo $eap_order->getOrderId(); ?>">
                        <?php echo $eap_order->getOrderId(); ?>
                    </a>
                </td>
                <td><?php echo $eap_order->getCreated(); ?></td>
                <td><?php echo $eap_order->getStatus(); ?></td>
            </tr>
            <tr>
                <td colspan=3><?php echo $eap_order->getBasketString(); ?></td>
            </tr>
<?php } ?>
    </table>
</div>

