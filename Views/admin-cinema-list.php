    <main>
        <h2 class="dash-title">Cinemas</h2>
        <hr>

        <?php if($success != null): ?>
        <div class="alert-container success-container">
            <i class="icon ion-md-checkmark"></i>
            <h3><?= $success; ?></h3>
        </div>
        <?php endif; ?>    

		<?php if($alert != null): ?>
		<div class="alert-container error-container">
			<i class="icon ion-md-close-circle-outline"></i>
			<h3><?= $alert; ?></h3>
			
			<?php if($cinemaId != null): ?>			
			<a href="<?= FRONT_ROOT ?>cinema/forceDisable/<?= $cinemaId ?>">
				<i class="icon ion-md-warning"></i>
				Force Disable
			</a>
			<?php endif; ?>			

		</div>
		<?php endif; ?>

		<a href="<?= FRONT_ROOT ?>cinema/listCinemaPath/all" class="btn-view-all">
			<i class="icon ion-md-clipboard"></i>
			List all cinemas (Including the disabled)
		</a>
        <div class="dashboard-container">
            <div class="content-container">				
				<table border="1">
					<thead>
						<tr>
							<th>Id</th>
							<th>Name</th>							
							<th>Address</th>							
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($cinemas as $cinema): ?>
							<tr>
								<td><?= $cinema->getId(); ?></td>
								<td><?= $cinema->getName(); ?></td>								
								<td><?= $cinema->getAddress(); ?></td>	
								<td>
									<div class="actions-container">

										<?php if ($cinema->getIsActive()): ?>							
										<a href="<?php echo FRONT_ROOT . "cinema/disable/" . $cinema->getId(); ?>" class="btn btn-disable">
											<i class="icon ion-md-trash"></i>
											Disable
										</a>		
										<?php else: ?>
										<a href="<?php echo FRONT_ROOT . "cinema/enable/" . $cinema->getId(); ?>" class="btn btn-enable">
											<i class="icon ion-md-done-all"></i>
											Enable
										</a>
										<?php endif; ?>		
																										
										<a href="<?php echo FRONT_ROOT . "cinema/modifyById/" . $cinema->getId(); ?>" class="btn">
											<i class="icon ion-md-build"></i>
											Modify
										</a>
									</div>
								</td>								
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
            </div>

        </div>
    </main>

</body>

</html>
