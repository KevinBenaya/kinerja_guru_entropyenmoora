<?php
$update = (isset($_GET['action']) AND $_GET['action'] == 'update') ? true : false;
if ($update) {
	$sql = $connection->query("SELECT * FROM kriteria WHERE id_kriteria='$_GET[key]'");
	$row = $sql->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$validasi = false; $err = false;
	if ($update) {
		$sql = "UPDATE kriteria SET id_penilaian=$_POST[id_penilaian], nama_kriteria='$_POST[nama_kriteria]', sifat_kriteria='$_POST[sifat_kriteria]' WHERE id_kriteria='$_GET[key]'";
	} else {
		$sql = "INSERT INTO kriteria VALUES (NULL, $_POST[id_penilaian], '$_POST[nama_kriteria]', '$_POST[sifat_kriteria]')";
		$validasi = true;
	}

	if ($validasi) {
		$q = $connection->query("SELECT id_kriteria FROM kriteria WHERE id_penilaian=$_POST[id_penilaian] AND nama_kriteria LIKE '%$_POST[nama_kriteria]%'");
		if ($q->num_rows) {
			echo alert("Kriteria sudah ada!", "?page=kriteria");
			$err = true;
		}
	}

  if (!$err AND $connection->query($sql)) {
		echo alert("Berhasil!", "?page=kriteria");
	} else {
		echo alert("Gagal!", "?page=kriteria");
	}
}

if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
  $connection->query("DELETE FROM kriteria WHERE id_kriteria='$_GET[key]'");
	echo alert("Berhasil!", "?page=kriteria");
}
?>
<div class="row">
	<div class="col-md-4">
	    <div class="panel panel-<?= ($update) ? "warning" : "info" ?>">
	        <div class="panel-heading"><h3 class="text-center"><?= ($update) ? "EDIT" : "TAMBAH" ?></h3></div>
	        <div class="panel-body">
	            <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
									<div class="form-group">
										<label for="id_penilaian">Jenis Penilaian</label>
										<select class="form-control" name="id_penilaian">
											<option>---</option>
											<?php $query = $connection->query("SELECT * FROM kategori_penilaian"); while ($data = $query->fetch_assoc()): ?>
												<option value="<?=$data["id_penilaian"]?>" <?= (!$update) ?: (($row["id_penilaian"] != $data["id_penilaian"]) ?: 'selected="on"') ?>><?=$data["jenis_penilaian"]?></option>
											<?php endwhile; ?>
										</select>
									</div>
	                <div class="form-group">
	                    <label for="nama_kriteria">Nama Kriteria</label>
	                    <input type="text" name="nama_kriteria" class="form-control" <?= (!$update) ?: 'value="'.$row["nama_kriteria"].'"' ?>>
	                </div>
									<div class="form-group">
	                  <label for="sifat_kriteria">Sifat</label>
										<select class="form-control" name="sifat_kriteria">
											<option>---</option>
											<option value="min" <?= (!$update) ?: (($row["sifat_kriteria"] != "min") ?: 'selected="on"') ?>>Min</option>
											<option value="max" <?= (!$update) ?: (($row["sifat_kriteria"] != "max") ?: 'selected="on"') ?>>Max</option>
										</select>
									</div>
	                <button type="submit" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block">Simpan</button>
	                <?php if ($update): ?>
										<a href="?page=kriteria" class="btn btn-info btn-block">Batal</a>
									<?php endif; ?>
	            </form>
	        </div>
	    </div>
	</div>
	<div class="col-md-8">
	    <div class="panel panel-info">
	        <div class="panel-heading"><h3 class="text-center">DAFTAR KRITERIA</h3></div>
	        <div class="panel-body">
	            <table class="table table-condensed">
	                <thead>
	                    <tr>
	                        <th>No</th>
	                        <th>Jenis Penilaian</th>
	                        <th>Kriteria</th>
	                        <th>Sifat</th>
	                        <th></th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <?php $no = 1; ?>
	                    <?php if ($query = $connection->query("SELECT a.nama_kriteria AS kriteria, b.jenis_penilaian AS jenis_penilaian, a.id_kriteria, a.sifat_kriteria FROM kriteria a JOIN kategori_penilaian b USING(id_penilaian)")): ?>
	                        <?php while($row = $query->fetch_assoc()): ?>
	                        <tr>
	                            <td><?=$no++?></td>
	                            <td><?=$row['jenis_penilaian']?></td>
	                            <td><?=$row['kriteria']?></td>
	                            <td><?=$row['sifat_kriteria']?></td>
	                            <td>
	                                <div class="btn-group">
	                                    <a href="?page=kriteria&action=update&key=<?=$row['id_kriteria']?>" class="btn btn-warning btn-xs">Edit</a>
	                                    <a href="?page=kriteria&action=delete&key=<?=$row['id_kriteria']?>" class="btn btn-danger btn-xs">Hapus</a>
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
