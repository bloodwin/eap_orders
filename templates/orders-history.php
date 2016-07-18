<?
	/*Шаблон для отображения содержимого истории заказов пользователя*/
	/*Данный шаблон можно разместить в папке используемого шаблона /wp-content/wp-recall/templates/ и он будет подключаться оттуда*/
?>
<?php global $eap_orders,$eap_order,$user_ID; ?>
<div class="order-data">
	<table>
		<tr>
			<th>Номер заказа</th>
			<th>Дата заказа</th>
			<th>Статус заказа</th>
		</tr>
		<?php foreach($eap_orders as $data){ eap_setup_orderdata($data); ?>
			<tr>
				<td>
					<a href="<?php echo rcl_format_url(get_author_posts_url($user_ID),'eap_orders'); ?>&order-id=<?php eap_order_ID(); ?>">
						<?php eap_order_ID(); ?>
					</a>
				</td>
				<td><?php eap_order_date(); ?></td>
                <td><?php eap_order_status($data->order_status); ?></td>
            </tr>
            <tr>
				<td colspan=3><?php eap_order_basket_full(); ?></td>
			</tr>
		<?php } ?>
	</table>
</div>

