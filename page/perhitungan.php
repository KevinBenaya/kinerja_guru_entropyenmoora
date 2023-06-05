<div class="row">
	<div class="col-md-12">
	<?php if (isset($_GET["kategori_penilaian"])) {
		$sqlKriteria = "";
		$namaKriteria = [];
		$queryKriteria = $connection->query("SELECT a.id_kriteria, a.nama_kriteria FROM kriteria a JOIN model b USING(id_kriteria) WHERE b.id_penilaian=$_GET[kategori_penilaian]");
		while ($kr = $queryKriteria->fetch_assoc()) {
			$sqlKriteria .= "SUM(
				IF(
					c.id_kriteria=".$kr["id_kriteria"].",
					IF(c.sifat='max', nilai.nilai/c.normalization, c.normalization/nilai.nilai), 0
				)
			) AS ".strtolower(str_replace(" ", "_", $kr["nama_kriteria"])).",";
			$namaKriteria[] = strtolower(str_replace(" ", "_", $kr["nama_kriteria"]));
		}
		$sql = "SELECT
			(SELECT nama_guru FROM guru WHERE nuptk=gr.nuptk) AS nama,
			(SELECT nuptk FROM guru WHERE nuptk=gr.nuptk) AS nuptk,
			$sqlKriteria
			SUM(
				IF(
						c.sifat = 'max',
						nilai.nilai / c.normalization,
						c.normalization / nilai.nilai
				) * c.bobot
			) AS rangking
		FROM
			nilai
			JOIN guru gr USING(nuptk)
			JOIN (
				SELECT
						nilai.id_kriteria AS id_kriteria,
						kriteria.sifat AS sifat,
						(
							SELECT bobot FROM model WHERE id_kriteria=kriteria.id_kriteria AND id_penilaian=kategori_penilaian.id_penilaian
						) AS bobot,
						ROUND(
							IF(kriteria.sifat='max', MAX(nilai.nilai), MIN(nilai.nilai)), 1
						) AS normalization
					FROM nilai
					JOIN kriteria USING(id_kriteria)
					JOIN kategori_penilaian ON kriteria.id_penilaian=kategori_penilaian.id_penilaian
					WHERE kategori_penilaian.id_penilaian=$_GET[kategori_penilaian]
				GROUP BY nilai.id_kriteria
			) c USING(id_kriteria)
		WHERE id_penilaian=$_GET[kategori_penilaian]
		GROUP BY nilai.nuptk
		ORDER BY rangking DESC"; ?>
	  <div class="panel panel-info">
	      <div class="panel-heading"><h3 class="text-center"><h2 class="text-center"><?php $query = $connection->query("SELECT * FROM kategori_penilaian WHERE id_penilaian=$_GET[kategori_penilaian]"); echo $query->fetch_assoc()["nama"]; ?></h2></h3></div>
	      <div class="panel-body">
	          <table class="table table-condensed table-hover">
	              <thead>
	                  <tr>
							<th>NIM</th>
							<th>Nama</th>
							<?php //$query = $connection->query("SELECT nama FROM kriteria WHERE id_penilaian=$_GET[kategori_penilaian]"); while($row = $query->fetch_assoc()): ?>
								<!-- <th><?//=$row["nama"]?></th> -->
							<?php //endwhile ?>
							<th>Nilai</th>
	                  </tr>
	              </thead>
	              <tbody>
					<?php $query = $connection->query($sql); while($row = $query->fetch_assoc()): ?>
					<?php
					$rangking = number_format((float) $row["rangking"], 8, '.', '');
					$q = $connection->query("SELECT nuptk FROM hasil WHERE nuptk='$row[nuptk]' AND id_penilaian='$_GET[kategori_penilaian]' AND tahun='$row[tahun]'");
					if (!$q->num_rows) {
					$connection->query("INSERT INTO hasil VALUES(NULL, '$_GET[kategori_penilaian]', '$row[nuptk]', '".$rangking."', '$row[tahun]')");
					}
					?>
					<tr>
						<td><?=$row["nuptk"]?></td>
						<td><?=$row["nama"]?></td>
						<?php for($i=0; $i<count($namaKriteria); $i++): ?>
						<!-- <th><?//=number_format((float) $row[$namaKriteria[$i]], 8, '.', '');?></th> -->
						<?php endfor ?>
						<td><?=$rangking?></td>
					</tr>
					<?php endwhile;?>
	              </tbody>
	          </table>
	      </div>
	  </div>
	<?php } else { ?>
		<h1>Kategori Penilaian belum dipilih...</h1>
	<?php } ?>
	</div>
</div>
