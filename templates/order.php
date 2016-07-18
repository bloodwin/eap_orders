<?
	/*Шаблон для отображения содержимого отдельного заказа,
	/*Данный шаблон можно разместить в папке используемого шаблона /wp-content/wp-recall/templates/ и он будет подключаться оттуда*/
?>
<?php global $eap_order,$eap_product; ?>
<div id="cart-form" class="cart-data">
	<table bordercolor="сссссс" border="1" cellpadding="5" class="order-data">
        <tr>
            <th colspan="4">Данные получателя</th> 
        </tr>
        <tr>
            <td colspan="4"><?php eap_order_fio(); ?></td> 
        </tr>
        <tr>
        <td colspan="4"><?php eap_order_address(); ?></td> 
        </tr>
        <tr>
            <td colspan="2"><?php eap_order_email(); ?></td> 
            <td colspan="2"><?php eap_order_phone(); ?></td> 
        </tr>
        <tr>
            <th colspan="4">Данные заказа</th> 
        </tr>
        <tr>
            <th>Создан</th> 
            <th>Статус</th> 
            <th>Статус изменен</th> 
            <th>Комментарий</th> 
        </tr>
        <tr>
            <td><?php eap_order_date(); ?></td> 
            <td><?php eap_order_status(); ?></td> 
            <td><?php eap_order_status_date(); ?></td> 
            <td><?php eap_order_user_comment(); ?></td> 
        </tr>
        <tr>
            <th colspan="4">Состав заказа</th> 
        </tr>
        <tr>
			<th class="product-name">Товар</th>
			<th width="70">Цена</th>
			<th class="product-number">Количество</th>
			<th width="70">Сумма</th>
        </tr>
		<?php foreach($eap_order->products as $eap_product): ?>
			<tr id="product-<?php eap_product_ID; ?>">
				<td>
                    <a href="<?php eap_product_permalink(); ?>"><?php eap_product_title(); ?></a>
                </td>
				<td><?php eap_product_price(); ?></td>
				<td align="center" data-product="<?php eap_product_ID; ?>">
					<span class="number-product"><?php eap_product_number(); ?></span>
				</td>
				<td class="sumprice-product"><?php eap_product_summ(); ?></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<th colspan="2"></th>
			<th>Общая сумма</th>
			<th class="cart-summa"><?php eap_order_price(); ?></th>
		</tr>
	</table>
</div>
