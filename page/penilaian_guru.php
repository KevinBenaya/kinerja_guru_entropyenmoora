<?php
$update = (isset($_GET['action']) AND $_GET['action'] == 'update') ? true : false;
if ($update) {
	$sql = $connection->query("SELECT * FROM penilaian_guru WHERE id_penilaian_guru='$_GET[key]'");
	$row = $sql->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$validasi = false; $err = false;
	if ($update) {
		$sql = "UPDATE penilaian_guru SET id_kriteria='$_POST[id_kriteria]', keterangan='$_POST[keterangan]', bobot='$_POST[bobot]' WHERE id_penilaian_guru='$_GET[key]'";
	} else {
		$sql = "INSERT INTO penilaian_guru VALUES (NULL, '$_POST[id_penilaian]', '$_POST[id_kriteria]', '$_POST[keterangan]', '$_POST[bobot]')";
		$validasi = true;
	}

	if ($validasi) {
		$q = $connection->query("SELECT id_penilaian_guru FROM penilaian_guru WHERE id_penilaian=$_POST[id_penilaian] AND id_kriteria=$_POST[id_kriteria] AND keterangan LIKE '%$_POST[keterangan]%' AND bobot=$_POST[bobot]");
		if ($q->num_rows) {
			echo alert("Penilaian sudah ada!", "?page=penilaian_guru");
			$err = true;
		}
	}

  if (!$err AND $connection->query($sql)) {
    echo alert("Berhasil!", "?page=penilaian_guru");
  } else {
		echo alert("Gagal!", "?page=penilaian_guru");
  }
}

if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
  $connection->query("DELETE FROM penilaian_guru WHERE id_penilaian_guru='$_GET[key]'");
	echo alert("Berhasil!", "?page=penilaian_guru");
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
										<select class="form-control" name="id_penilaian" id="kategori_penilaian">
											<option>---</option>
											<?php $sql = $connection->query("SELECT * FROM kategori_penilaian") ?>
											<?php while ($data = $sql->fetch_assoc()): ?>
												<option value="<?=$data["id_penilaian"]?>" <?= (!$update) ?: (($row["id_penilaian"] != $data["id_penilaian"]) ?: 'selected="selected"') ?>><?=$data["jenis_penilaian"]?></option>
											<?php endwhile; ?>
										</select>
									</div>
									<div class="form-group">
	                  <label for="id_kriteria">Kriteria</label>
										<select class="form-control" name="id_kriteria" id="kriteria">
											<option>---</option>
											<?php $sql = $connection->query("SELECT * FROM kriteria") ?>
											<?php while ($data = $sql->fetch_assoc()): ?>
												<option value="<?=$data["id_kriteria"]?>" class="<?=$data["id_penilaian"]?>" <?= (!$update) ?: (($row["id_kriteria"] != $data["id_kriteria"]) ?: 'selected="selected"') ?>><?=$data["nama_kriteria"]?></option>
											<?php endwhile; ?>
										</select>
									</div>
	                <div class="form-group">
	                    <label for="keterangan">Keterangan</label>
	                    <input type="text" name="keterangan" class="form-control" <?= (!$update) ?: 'value="'.$row["keterangan"].'"' ?>>
	                </div>
	                <div class="form-group">
	                    <label for="bobot">Bobot</label>
	                    <input type="text" name="bobot" class="form-control" <?= (!$update) ?: 'value="'.$row["bobot"].'"' ?>>
	                </div>
	                <button type="submit" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block">Simpan</button>
	                <?php if ($update): ?>
										<a href="?page=penilaian_guru" class="btn btn-info btn-block">Batal</a>
									<?php endif; ?>
	            </form>
	        </div>
	    </div>
	</div>
	<div class="col-md-8">
	    <div class="panel panel-info">
	        <div class="panel-heading"><h3 class="text-center">DAFTAR</h3></div>
	        <div class="panel-body">
	            <table class="table table-condensed">
	                <thead>
	                    <tr>
	                        <th>No</th>
	                        <th>Jenis Penilaian</th>
	                        <th>Kriteria</th>
	                        <th>Keterangan</th>
	                        <th>Bobot</th>
	                        <th></th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <?php $no = 1; ?>
	                    <?php if ($query = $connection->query("SELECT a.id_penilaian_guru, c.jenis_penilaian AS jenis_penilaianS, b.nama_kriteria AS nama_kriteria, a.keterangan, a.bobot FROM penilaian_guru a JOIN kriteria b ON a.id_kriteria=b.id_kriteria JOIN kategori_penilaian c ON a.id_penilaian=c.id_penilaian")): ?>
	                        <?php while($row = $query->fetch_assoc()): ?>
	                        <tr>
	                            <td><?=$no++?></td>
	                            <td><?=$row['jenis_penilaian']?></td>
	                            <td><?=$row['nama_kriteria']?></td>
	                            <td><?=$row['keterangan']?></td>
	                            <td><?=$row['bobot']?></td>
	                            <td>
	                                <div class="btn-group">
	                                    <a href="?page=penilaian_guru&action=update&key=<?=$row['id_penilaian_guru']?>" class="btn btn-warning btn-xs">Edit</a>
	                                    <a href="?page=penilaian_guru&action=delete&key=<?=$row['id_penilaian_guru']?>" class="btn btn-danger btn-xs">Hapus</a>
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

<script type="text/javascript">
$("#kriteria").chained("#beasiswa");
</script>
