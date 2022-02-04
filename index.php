<?php
	session_start();

	$adress		=	"<--------->";  //Navicat IP
	$username	=	"<--------->";  //Navicat Username (default: root)
	$password	=	"<--------->";  //Navicat Password
	
	$admin_password='test';
	error_reporting(E_ERROR | E_PARSE);
	$connection = new PDO("mysql:host=$adress;", $username, $password);
	$autentificator = isset($_GET['auth']) ? $_GET['auth'] : null;
	if($autentificator=='logout')
	{
		session_destroy();
		session_unset();
		print'<script> location.replace("index.php"); </script>';
	}

	function getevents()
	{
		global $connection;
		
		$stmt = $connection->prepare("SELECT * FROM account.events WHERE event_date > NOW()");
		$stmt->execute();
		if($stmt->rowCount() > 0)
			return $result = $stmt->fetchAll();
		else
			return 0;
	}
	function RemoveEvent($id)
	{
		global $connection;
		
		$stmt = $connection->prepare("DELETE FROM account.events WHERE id=:datarem");
		$stmt->execute(array(':datarem'=>$id));
		print "<div class='alert alert-success'>Eveniment sters cu succes!</div>";
	}
	
	function InserttoEvent($named, $datetimed)
	{
		global $connection;
		
		$datetime_ins = date("Y-m-d H:i:s", strtotime($datetimed));
		$stmt = $connection->prepare("INSERT INTO account.events (name, event_date) VALUES ('$named', '$datetime_ins')");
		$stmt->execute();
		print "<div class='alert alert-success'>Eveniment adaugat cu succes!</div>";
		print '<script> location.replace("index.php"); </script>';
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="style/bootstrap.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<title>Calendar de Evenimente</title>
	</head>
	<body>
		<center style="margin-top:100px;">
			<div class="col-6">
				<div class="card">
					<h5 class="card-header">
						<div class="row">
							<div class="col-3">
							</div>
							<div class="col-6">
							Calendar de evenimente
							</div>
							<div class="col-3">
							<?php 
								if(!isset($_SESSION['status']))
									print '<a href="?auth=admin"><i style="color: black;" class="fa fa-user"></i></a>';
								else
								{
									print '<a href="?auth=logout"><i style="color: red;" class="fa fa-sign-out"></i></a>&nbsp;&nbsp;&nbsp;';
									print '<a href="?auth=add"><i style="color: black;" class="fa fa-plus"></i></a>';
								}
							?>
							</div>
						</div>
					</h5>
					<div class="card-body"> 
						<p class="card-text">
						<?php
									if($autentificator=='admin' && $_SESSION['status']!='verified')
									{
										if(isset($_POST['submit_button']))
										{
											if($_POST['admin_password']==$admin_password)
											{
												$_SESSION['status']='verified';
												print '<div class="alert alert-success">Autentificat cu succes</div>';
												print '<script> location.replace("index.php"); </script>';
											}
											else
											{
												print '<div class="alert alert-danger">Parola este gresita!</div>';
											}
										}
									?>
									<form method="POST">
										<input type="password" class="form-control" name="admin_password">
										<button type="submit" class="btn btn-success" name="submit_button">Autentificare</button>
									</form>
									<?php
									die();
									}
									if($autentificator=='add' && $_SESSION['status']=='verified')
									{
										if(isset($_POST['insertfor_event']))
										{
											InserttoEvent($_POST['event_named'], $_POST['event_datetime']);
										}
									?>
									<form method="POST">
										<label for="event_name">Nume eveniment:</label>
										<input type="text" style="max-width:30%;" class="form-control" name="event_named" placeholder="Nume eveniment"><br>
										<label for="event_datetime">Data & Ora eveniment:</label>
										<input type="datetime-local" style="max-width:30%;" class="form-control" name="event_datetime">
										<hr>
										<button type="submit" name="insertfor_event" class="btn btn-success">Adauga eveniment</button>
									</form>
									<?php
									die();
									}
									
									if(isset($_POST['submit_trashevent']))
									{
										RemoveEvent($_POST['id_event']);
									}
									if(getevents()!=0)
									{
										print '
										<table class="table">
										<thead class="table-dark">
										<tr>
										<th scope="col">
										<center>#</center>
										</th>
										<th scope="col">
										<center>Nume eveniment</center>
										</th>
										<th scope="col">
										<center>Data & Ora</center>
										</th>
										<th scope="col">
										<center>Timp ramas</center>
										</th>';
										if(isset($_SESSION['status']))
										print'
									    <th scope="col">
										<center>Actiuni</center>
										</th>';
										print'
										</tr>
										</thead>
										<tbody>';
										$list=array();
										$list=getevents();
										$conter=0;
										foreach ($list as $lista)
										{ 
										$datatimp = date("d-m-Y H:i:s", strtotime($lista['event_date']));
										$datatimpjs = date("m-d-Y H:i:s", strtotime($lista['event_date']));
										$indentifier= substr($lista['name'], 1, 3);
										?> 
										
										<tr>
											<th scope="row">
												<center> <?php print $conter=$conter+1; ?> </center>
											</th>
											<td>
												<center> <?php print $lista['name']; ?> </center>
											</td>
											<td>
												<center> <?php print $datatimp; ?> </center>
											</td>
											<td>
												<center> <p id="cooldown-<?php print $indentifier.$conter; ?>"></p> </center>
											</td>
											<?php
											if(isset($_SESSION['status']))
											{?>
											<td>
											<form method="POST">
												<input type="hidden" value="<?php print $lista['id']; ?>" name="id_event">
												<center> <button type="submit" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;" name="submit_trashevent"><i style="color:red;" class="fa fa-trash"></i></button></center>
											</form>
											</td>
											<?php
											}
											?>
										</tr> 
										<script>
										var countDownDate<?php print $indentifier.$conter; ?> = new Date("<?php print $datatimpjs; ?>").getTime();
										var x = setInterval(function() {
										var now = new Date().getTime();
										var distance = countDownDate<?php print $indentifier.$conter; ?> - now;
										var days = Math.floor(distance / (1000 * 60 * 60 * 24));
										var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
										var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
										var seconds = Math.floor((distance % (1000 * 60)) / 1000);
										document.getElementById("cooldown-<?php print $indentifier.$conter; ?>").innerHTML = days + "d " + hours + "h "
										+ minutes + "min " + seconds + "sec ";
										if (distance < 0) {
											clearInterval(x);
											document.getElementById("cooldown-<?php print $indentifier.$conter; ?>").innerHTML = "Evenimentul a avut loc!";
										}}, 1000);
										</script>
										<?php
										}
										print '
										</tbody>
										</table>';
									}
									else 
									{
										print '<div class="alert alert-warning">Nu exista evenimente anuntate!</div>';
									}
								?>
						</p>
					</div>
				</div>
			</div>
		</center>
	</body>
</html>