<?php
$monthDays = date('t');
$firstWeekDay = date('N',strtotime(date('Ym01')));
$temp = range(1, $monthDays);
for ($i = 0; $i < $firstWeekDay - 1; $i ++) {
    array_unshift($temp, 0);
}

$arr = array_chunk($temp, 7);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Insert title here</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<link href="https://cdn.bootcss.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

	<div id="app" class="container">
		<h1>Hello App!<small>这是<?php echo date('Y年m月');?></small></h1>
		<div class="row">
			<table class="table">
				<caption>Optional table caption.</caption>
				<thead>
					<tr>
						<th>星期一</th>
						<th>星期二</th>
						<th>星期三</th>
						<th>星期四</th>
						<th>星期五</th>
						<th>星期六</th>
						<th>星期日</th>
					</tr>
				</thead>
      <?php foreach($arr as $row){?>
          <tr>
          <?php foreach($row as $item){?>
          <td><?php echo $item?:'';?></td>
          <?php }?>
          </tr>
      <?php }?>
      <tbody>
				</tbody>
			</table>
		</div>
	</div>

	<script src="https://cdn.bootcss.com/jquery/2.2.0/jquery.min.js"></script>
	<script src="https://cdn.bootcss.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
	<script src="https://cdn.bootcss.com/vue/1.0.15/vue.min.js"></script>

</body>
</html>