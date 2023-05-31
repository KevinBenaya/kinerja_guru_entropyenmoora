<?php
$update = (isset($_GET['action']) AND $_GET['action'] == 'update') ? true : false;
if ($update) {
	$sql = $connection->query("SELECT * FROM nilai JOIN penilaian_guru USING(id_kriteria) WHERE id_nilai='$_GET[key]'");
	$row = $sql->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_POST["save"])) {
	$validasi = false; $err = false;
	if ($update) {
		$sql = "UPDATE nilai SET id_kriteria='$_POST[id_kriteria]', nuptk='$_POST[nuptk]', nilai='$_POST[nilai]' WHERE id_nilai='$_GET[key]'";
	} else {
		$query = "INSERT INTO nilai VALUES ";
		foreach ($_POST["nilai"] as $id_kriteria => $nilai) {
			$query .= "(NULL, '$_POST[id_penilaian]', '$id_kriteria', '$_POST[nuptk]', '$nilai'),";
		}
		$sql = rtrim($query, ',');
		$validasi = true;
	}

	if ($validasi) {
		foreach ($_POST["nilai"] as $id_kriteria => $nilai) {
			$q = $connection->query("SELECT id_nilai FROM nilai WHERE id_penilaian=$_POST[id_penilaian] AND id_kriteria=$id_kriteria AND nuptk=$_POST[nuptk] AND nilai LIKE '%$nilai%'");
			if ($q->num_rows) {
				echo alert("Nilai untuk ".$_POST["nuptk"]." sudah ada!", "?page=nilai");
				$err = true;
			}
		}
	}

  if (!$err AND $connection->query($sql)) {
		echo alert("Berhasil!", "?page=nilai");
	} else {
		echo alert("Gagal!", "?page=nilai");
	}
}

if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
  $connection->query("DELETE FROM nilai WHERE id_nilai='$_GET[key]'");
	echo alert("Berhasil!", "?page=nilai");
}
?>
<div class="row">
	<div class="col-md-4">
	    <div class="panel panel-<?= ($update) ? "warning" : "info" ?>">
	        <div class="panel-heading"><h3 class="text-center"><?= ($update) ? "EDIT" : "TAMBAH" ?></h3></div>
	        <div class="panel-body">
	            <form action="<?=$_SERVER["REQUEST_URI"]?>" method="post">
									<div class="form-group">
										<label for="nuptk">Guru</label>
										<?php if ($_POST): ?>
											<input type="text" name="nuptk" value="<?=$_POST["nuptk"]?>" class="form-control" readonly="on">
										<?php else: ?>
											<select class="form-control" name="nuptk">
												<option>---</option>
												<?php $sql = $connection->query("SELECT * FROM guru"); while ($data = $sql->fetch_assoc()): ?>
													<option value="<?=$data["nuptk"]?>" <?= (!$update) ? "" : (($row["nuptk"] != $data["nuptk"]) ? "" : 'selected="selected"') ?>><?=$data["nuptk"]?> | <?=$data["nama_guru"]?></option>
												<?php endwhile; ?>
											</select>
										<?php endif; ?>
									</div>
									<div class="form-group">
	                  <label for="id_penilaian">Jenis Penilaian</label>
										<?php if ($_POST): ?>
											<?php $q = $connection->query("SELECT jenis_penilaian FROM kategori_penilaian WHERE id_penilaian=$_POST[id_penilaian]"); ?>
											<input type="text"value="<?=$q->fetch_assoc()["jenis_penilaian"]?>" class="form-control" readonly="on">
											<input type="hidden" name="id_penilaian" value="<?=$_POST["id_penilaian"]?>">
										<?php else: ?>
											<select class="form-control" name="id_penilaian" id="kategori_penilaian">
												<option>---</option>
												<?php $sql = $connection->query("SELECT * FROM kategori_penilaian"); while ($data = $sql->fetch_assoc()): ?>
													<option value="<?=$data["id_penilaian"]?>"<?= (!$update) ? "" : (($row["id_penilaian"] != $data["id_penilaian"]) ? "" : 'selected="selected"') ?>><?=$data["jenis_penilaian"]?></option>
												<?php endwhile; ?>
											</select>
										<?php endif; ?>
									</div>
									<?php if ($_POST): ?>
										<?php $q = $connection->query("SELECT * FROM kriteria WHERE id_penilaian=$_POST[id_penilaian]"); while ($r = $q->fetch_assoc()): ?>
				                <div class="form-group">
					                  <label for="nilai"><?=ucfirst($r["nama_kriteria"])?></label>
														<select class="form-control" name="nilai[<?=$r["id_kriteria"]?>]" id="nilai">
															<option>---</option>
															<?php $sql = $connection->query("SELECT * FROM penilaian_guru WHERE id_kriteria=$r[id_kriteria]"); while ($data = $sql->fetch_assoc()): ?>
																<option value="<?=$data["bobot"]?>" class="<?=$data["id_kriteria"]?>"<?= (!$update) ? "" : (($row["kd_penilaian_guru"] != $data["kd_penilaian_guru"]) ? "" : ' selected="selected"') ?>><?=$data["keterangan"]?></option>
															<?php endwhile; ?>
														</select>
				                </div>
										<?php endwhile; ?>
										<input type="hidden" name="save" value="true">
									<?php endif; ?>
	                <button type="submit" id="simpan" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block"><?=($_POST) ? "Simpan" : "Tampilkan"?></button>
	                <?php if ($update): ?>
										<a href="?page=nilai" class="btn btn-info btn-block">Batal</a>
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
							<th>NUPTK</th>
							<th>Nama Guru</th>
	                        <th>Jenis Penilaian</th>
	                        <th>Kriteria</th>
	                        <th>Nilai</th>
	                        <th></th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <?php $no = 1; ?>
	                    <?php if ($query = $connection->query("SELECT a.id_nilai, c.jenis_penilaian AS jenis_penilaian, b.nama_kriteria AS nama_kriteria, d.nuptk, d.nama_guru AS nama_gur, a.nilai FROM nilai a JOIN kriteria b ON a.id_kriteria=b.id_kriteria JOIN kategori_penilaian c ON a.id_penilaian=c.id_penilaian JOIN guru d ON d.nuptk=a.nuptk")): ?>
	                        <?php while($row = $query->fetch_assoc()): ?>
	                        <tr>
	                            <td><?=$no++?></td>
								<td><?=$row['nuptk']?></td>
								<td><?=$row['nama_guru']?></td>
	                            <td><?=$row['jenis_penilaian']?></td>
	                            <td><?=$row['nama_kriteria']?></td>
	                            <td><?=$row['nilai']?></td>
	                            <td>
	                                <div class="btn-group">
	                                    <a href="?page=nilai&action=update&key=<?=$row['id_nilai']?>" class="btn btn-warning btn-xs">Edit</a>
	                                    <a href="?page=nilai&action=delete&key=<?=$row['id_nilai']?>" class="btn btn-danger btn-xs">Hapus</a>
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
$("#kriteria").chained("#kategori_penilaian");
$("#nilai").chained("#kriteria");
</script>
