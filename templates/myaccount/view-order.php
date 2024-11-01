<?php
if ($tracking_items) : ?>
	<table>
		<thead>
			<tr>
				<th class="courier"><span class="nobr"><?php _e('Courier'); ?></span></th>
				<th class="tracking-number"><span class="nobr"><?php _e('Tracking Number'); ?></span></th>
				<th class="tracking-url"><?php _e('Actions'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($tracking_items as $key => $tracking_item) {
			?>
				<tr class="tracking">
					<td class="courier" data-title="<?php _e('Courier'); ?>">
						<?php echo esc_html('DTDC'); ?>
					</td>
					<td class="tracking-number" data-title="<?php _e('Tracking Number'); ?>">
						<?php  _e($tracking_item->shipsy_refno); ?>
					</td>
					<?php if ($tracking_item->track_url) { ?>
						<td class="tracking-url" style="text-align: center;">
							<a target="_blank" href="<?php echo esc_url($tracking_item->track_url); ?>"><button>Track Order</button></a>
						</td>
					<?php } ?>
				</tr>
			<?php
			}
			?>
		</tbody>
	</table>
<?php
endif; ?>