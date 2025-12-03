<?php
session_start();
if (!isset($_SESSION['id_usuario'])) { header('Location: ../crud-usuarios/login.php'); exit; }
require __DIR__ . '/../connect/index.php'; 

$aluno = $_GET['aluno'] ?? '';
$ano   = $_GET['year']  ?? '';
$turma = $_GET['turma'] ?? 'all';

$alunos = $conn->query("SELECT id_aluno, nome FROM aluno ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$turmas = $conn->query("SELECT id_turma, nome_turma FROM turma ORDER BY nome_turma")->fetchAll(PDO::FETCH_ASSOC);
$anos   = $conn->query("SELECT DISTINCT YEAR(data_presenca) FROM presenca ORDER BY 1 DESC")->fetchAll(PDO::FETCH_COLUMN);

$report = null;
if ($aluno && $ano) {
    $where = "WHERE p.id_aluno = :aluno AND YEAR(p.data_presenca) = :ano";
    $params = [':aluno'=>$aluno, ':ano'=>$ano];
    if ($turma !== 'all' && $turma !== '') { $where .= " AND p.id_turma = :turma"; $params[':turma']=$turma; }

    $sql = "SELECT 
                COUNT(*) AS total,
                SUM(p.status='presente') AS presentes,
                SUM(p.status='falta') AS faltas,
                SUM(p.status='atraso') AS atrasos
            FROM presenca p
            $where";
    $stmt = $conn->prepare($sql); $stmt->execute($params); $tot = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT MONTH(p.data_presenca) AS mes, COUNT(*) AS total, SUM(p.status='presente') AS presentes
            FROM presenca p
            $where
            GROUP BY mes ORDER BY mes";
    $stmt = $conn->prepare($sql); $stmt->execute($params); $monthly = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT p.id_presenca, p.data_presenca, p.hora, p.status, p.observacao, t.nome_turma
            FROM presenca p JOIN turma t ON t.id_turma = p.id_turma
            $where ORDER BY p.data_presenca DESC, p.hora DESC";
    $stmt = $conn->prepare($sql); $stmt->execute($params); $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $al = $conn->prepare("SELECT nome, matricula FROM aluno WHERE id_aluno = :id LIMIT 1");
    $al->execute([':id'=>$aluno]); $alunoInfo = $al->fetch(PDO::FETCH_ASSOC);

    $report = ['tot'=>$tot, 'monthly'=>$monthly, 'records'=>$records, 'aluno'=>$alunoInfo];
}

$meses = [1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'];
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Relatório por Aluno (simples)</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
  <h4>Relatório por Aluno</h4>

  <form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
      <select name="aluno" class="form-select" required>
        <option value="">Escolha o aluno</option>
        <?php foreach($alunos as $a): ?>
          <option value="<?= $a['id_aluno'] ?>" <?= ($aluno == $a['id_aluno']) ? 'selected' : '' ?>><?= htmlspecialchars($a['nome']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <select name="year" class="form-select" required>
        <option value="">Ano</option>
        <?php foreach($anos as $y): ?>
          <option value="<?= $y ?>" <?= ($ano == $y) ? 'selected' : '' ?>><?= $y ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4">
      <select name="turma" class="form-select">
        <option value="all">Todas as turmas</option>
        <?php foreach($turmas as $t): ?>
          <option value="<?= $t['id_turma'] ?>" <?= ($turma == $t['id_turma']) ? 'selected' : '' ?>><?= $t['nome_turma'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-1"><button class="btn btn-primary w-100">analisar</button></div>
    <div class="col-md-1"><a class="btn btn-primary w-100"  style="color: white; text-decoration: none;" href="index.php">voltar</a></div>
  </form>

  <?php if ($report): ?>
    <div class="mb-2"><strong><?= $report['aluno']['nome'] ?? 'escolher' ?></strong>
    <div class="row mb-3">
      <div class="col-3"><div class="border p-2">Total: <?= $report['tot']['total'] ?? 0 ?></div></div>
      <div class="col-3"><div class="border p-2 text-success">Presenças: <?= $report['tot']['presentes'] ?? 0 ?></div></div>
      <div class="col-3"><div class="border p-2 text-success">Faltas: <?= $report['tot']['faltas'] ?? 0 ?></div></div>
      <div class="col-3"><div class="border p-2 text-success">Atrasos: <?= $report['tot']['atrasos'] ?? 0 ?></div></div>
    </div>

    <h6>Resumo mensal</h6>
    <table class="table table-sm mb-3">
      <thead><tr><th>Mês</th><th>Total</th><th>Presenças</th><th>%</th></tr></thead>
      <tbody>
        <?php foreach($report['monthly'] as $m): 
          $pct = ($m['total']>0) ? round(($m['presentes']/$m['total'])*100,2) : '—';
        ?>
          <tr>
            <td><?= $meses[(int)$m['mes']] ?? $m['mes'] ?></td>
            <td><?= $m['total'] ?></td>
            <td><?= $m['presentes'] ?></td>
            <td><?= is_numeric($pct) ? $pct.' %' : $pct ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <h6>Registros</h6>
    <table class="table table-sm">
      <thead><tr><th>ID</th><th>Data</th><th>Hora</th><th>Turma</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach($report['records'] as $r): ?>
          <tr>
            <td><?= $r['id_presenca'] ?></td>
            <td><?= $r['data_presenca'] ?></td>
            <td><?= $r['hora'] ?></td>
            <td><?= $r['nome_turma'] ?></td>
            <td><?= $r['status'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
