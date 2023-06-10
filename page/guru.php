<?php
$update = (isset($_GET['action']) AND $_GET['action'] == 'update') ? true : false;
if ($update) {
	$sql = $connection->query("SELECT * FROM guru WHERE nuptk='$_GET[key]'");
	$row = $sql->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$validasi = false; $err = false;
	if ($update) {
		$sql = "UPDATE guru SET nuptk='$_POST[nuptk]', nama_guru='$_POST[nama_guru]',  jenis_kelamin='$_POST[jenis_kelamin]' WHERE nuptk='$_GET[key]'";
	} else {
		$sql = "INSERT INTO guru VALUES ('$_POST[nuptk]', '$_POST[nama_guru]', '$_POST[jenis_kelamin]')";
		$validasi = true;
	}

	if ($validasi) {
		$q = $connection->query("SELECT nuptk FROM guru WHERE nuptk=$_POST[nuptk]");
		if ($q->num_rows) {
			echo alert($_POST["nuptk"]." sudah terdaftar!", "?page=guru");
			$err = true;
		}
	}

  if (!$err AND $connection->query($sql)) {
    echo alert("Berhasil!", "?page=guru");
  }
   else {
		echo alert($err);
  }
}

if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
  $connection->query("DELETE FROM guru WHERE nuptk=$_GET[key]");
	echo alert("Berhasil!", "?page=guru");
}
?>
<div class="row">
	<div class="col-md-4">
	    <div class="panel panel-<?= ($update) ? "warning" : "info" ?>">
	        <div class="panel-heading"><h3 class="text-center"><?= ($update) ? "EDIT" : "TAMBAH" ?></h3></div>
	        <div class="panel-body">
	            <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
	                <div class="form-group">
	                    <label for="nuptk">NUPTK</label>
	                    <input type="text" name="nuptk" class="form-control" <?= (!$update) ?: 'value="'.$row["nuptk"].'"' ?>>
	                </div>
	                <div class="form-group">
	                    <label for="nama_guru">Nama Lengkap</label>
	                    <input type="text" name="nama_guru" class="form-control" <?= (!$update) ?: 'value="'.$row["nama_guru"].'"' ?>>
	                </div>
									<div class="form-group">
	                  <label for="jenis_kelamin">Jenis Kelamin</label>
										<select class="form-control" name="jenis_kelamin">
											<option>---</option>
											<option value="Laki-laki" <?= (!$update) ?: (($row["jenis_kelamin"] != "Laki-laki") ?: 'selected="on"') ?>>Laki-laki</option>
											<option value="Perempuan" <?= (!$update) ?: (($row["jenis_kelamin"] != "Perempuan") ?: 'selected="on"') ?>>Perempuan</option>
										</select>
									</div>
	                <button type="submit" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block">Simpan</button>
	                <?php if ($update): ?>
										<a href="?page=guru" class="btn btn-info btn-block">Batal</a>
									<?php endif; ?>
	            </form>
	        </div>
	    </div>
	</div>
	<div class="col-md-8">
	    <div class="panel panel-info">
	        <div class="panel-heading"><h3 class="text-center">DAFTAR GURU</h3></div>
	        <div class="panel-body">
	            <table class="table table-condensed">
	                <thead>
	                    <tr>
	                        <th>No</th>
	                        <th>NUPTK</th>
	                        <th>Nama</th>
	                        <th>Jenis Kelamin</th>
	                        <th></th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <?php $no = 1; ?>
	                    <?php if ($query = $connection->query("SELECT * FROM guru")): ?>
	                        <?php while($row = $query->fetch_assoc()): ?>
	                        <tr>
	                            <td><?=$no++?></td>
	                            <td><?=$row['nuptk']?></td>
	                            <td><?=$row['nama_guru']?></td>
	                            <td><?=$row['jenis_kelamin']?></td>
	                            <td>
	                                <div class="btn-group">
	                                    <a href="?page=guru&action=update&key=<?=$row['nuptk']?>" class="btn btn-warning btn-xs">Edit</a>
	                                    <a href="?page=guru&action=delete&key=<?=$row['nuptk']?>" class="btn btn-danger btn-xs">Hapus</a>
	                                </div>
	                            </td>
	                        </tr>
	                        <?php endwhile ?>
	                    <?php endif ?>
	                </tbody>
	            </table>
	        </div>
	    </div>
	</div>
</div>
