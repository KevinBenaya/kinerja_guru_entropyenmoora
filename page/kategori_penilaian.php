<?php
$update = (isset($_GET['action']) AND $_GET['action'] == 'update') ? true : false;
if ($update) {
	$sql = $connection->query("SELECT * FROM kategori_penilaian WHERE id_penilaian='$_GET[key]'");
	$row = $sql->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$validasi = false; $err = false;
	if ($update) {
		$sql = "UPDATE kategori_penilaian SET jenis_penilaian='$_POST[jenis_penilaian]' WHERE id_penilaian='$_GET[key]'";
	} else {
		$sql = "INSERT INTO kategori_penilaian VALUES (NULL, '$_POST[jenis_penilaian]')";
		$validasi = true;
	}

	if ($validasi) {
		$q = $connection->query("SELECT id_penilaian FROM kategori_penilaian WHERE jenis_penilaian LIKE '%$_POST[jenis_penilaian]%'");
		if ($q->num_rows) {
			echo alert("Kategori penilaian sudah ada!", "?page=kategori_penilaian");
			$err = true;
		}
	}

  if (!$err AND $connection->query($sql)) {
    echo alert("Berhasil!", "?page=kategori_penilaian");
  } else {
		echo alert("Gagal!", "?page=kategori_penilaian");
  }
}

if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
  $connection->query("DELETE FROM kategori_penilaian WHERE id_penilaian='$_GET[key]'");
	echo alert("Berhasil!", "?page=kategori_penilaian");
}
?>
<div class="row">
	<div class="col-md-4">
	    <div class="panel panel-<?= ($update) ? "warning" : "info" ?>">
	        <div class="panel-heading"><h3 class="text-center"><?= ($update) ? "EDIT" : "TAMBAH" ?></h3></div>
	        <div class="panel-body">
	            <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
	                <div class="form-group">
	                    <label for="jenis_penilaian">Jenis Penilaian</label>
	                    <input type="text" name="jenis_penilaian" class="form-control" <?= (!$update) ?: 'value="'.$row["jenis_penilaian"].'"' ?>>
	                </div>
	                <button type="submit" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block">Simpan</button>
	                <?php if ($update): ?>
										<a href="?page=kategori_penilaian" class="btn btn-info btn-block">Batal</a>
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
	                        <th></th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <?php $no = 1; ?>
	                    <?php if ($query = $connection->query("SELECT * FROM kategori_penilaian")): ?>
	                        <?php while($row = $query->fetch_assoc()): ?>
	                        <tr>
	                            <td><?=$no++?></td>
	                            <td><?=$row['jenis_penilaian']?></td>
	                            <td>
	                                <div class="btn-group">
	                                    <a href="?page=kategori_penilaian&action=update&key=<?=$row['id_penilaian']?>" class="btn btn-warning btn-xs">Edit</a>
	                                    <a href="?page=kategori_penilaian&action=delete&key=<?=$row['id_penilaian']?>" class="btn btn-danger btn-xs">Hapus</a>
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
