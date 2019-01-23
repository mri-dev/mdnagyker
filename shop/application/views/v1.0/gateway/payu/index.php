<?	
// IPN
if( $this->gets[2] == 'ipn' ): ?>
	<div class="payu-msg page-width">
		<div class="responsive-view full-width">
			<?=$this->ipn_data?>
		</div>
	</div>
<? endif; ?>

<?	
// BACKREF
if( $this->gets[2] == 'backref' ): ?>
	<div class="payu-msg page-width">
		<div class="responsive-view full-width">
			<?=$this->pay_msg?>
		</div>
	</div>
<? endif; ?>
<?	
// TIMEOUT
if( $this->gets[2] == 'timeout' ): ?>
	<div class="payu-msg page-width">
		<div class="responsive-view full-width">
			<?=$this->pay_msg?>
		</div>
	</div>
<? endif; ?>
