<div class="alerts">
	<?php foreach ($alerts as $a): ?>
	<div class="alert alert-<?php echo $a['type']; ?>">
		<p><?php echo $a['message']; ?></p>
	</div>
	<?php endforeach; ?>
</div>