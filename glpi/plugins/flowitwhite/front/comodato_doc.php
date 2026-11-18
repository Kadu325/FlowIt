<?php
/**
 * FlowIT - Geração do Termo de Comodato e Responsabilidade (Notebook e Smartphone)
 * Modelo oficial Fazenda Progresso com integração da Assinatura Digital
 */

require_once __DIR__ . '/bootstrap.php';

global $DB;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

if (!$id) {
    die("Contrato de comodato não especificado.");
}

$iterator = $DB->request([
    'FROM' => 'glpi_plugin_flowit_comodato',
    'WHERE' => ['id' => $id]
]);

$data = null;
foreach ($iterator as $row) {
    $data = $row;
    break;
}

if (!$data) {
    die("Termo de comodato não localizado.");
}

// Formatação de data
$meses = [
    '01' => 'janeiro', '02' => 'fevereiro', '03' => 'março', '04' => 'abril',
    '05' => 'maio', '06' => 'junho', '07' => 'julho', '08' => 'agosto',
    '09' => 'setembro', '10' => 'outubro', '11' => 'novembro', '12' => 'dezembro'
];
$ts = strtotime($data['assigned_date'] ?: date('Y-m-d'));
$dia = date('d', $ts);
$mes = $meses[date('m', $ts)];
$ano = date('Y', $ts);
$data_formatada = "Mucugê - Ba, {$dia}/" . date('m', $ts) . "/{$ano}.";

$userName = htmlspecialchars($data['user_name'] ?: 'NOME DO PARCEIRO', ENT_QUOTES, 'UTF-8');
$userCpf = htmlspecialchars($data['user_cpf'] ?: '(DOC)', ENT_QUOTES, 'UTF-8');
$contractType = $data['contract_type']; // 'notebook' ou 'smartphone'
$assetName = htmlspecialchars($data['asset_name'] ?: '', ENT_QUOTES, 'UTF-8');
$assetSerial = htmlspecialchars($data['asset_serial'] ?: 'NÃO INFORMADO', ENT_QUOTES, 'UTF-8');
$assetModel = htmlspecialchars($data['asset_model'] ?: '', ENT_QUOTES, 'UTF-8');
$accessories = htmlspecialchars($data['accessories'] ?: '', ENT_QUOTES, 'UTF-8');

$isSigned = ($data['signature_status'] === 'assinado');
$signedAt = !empty($data['signed_at']) ? date('d/m/Y H:i:s', strtotime($data['signed_at'])) : null;
$signatureImg = $data['signature_image'] ?? null;
$signerIp = htmlspecialchars($data['signer_ip'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>Termo de Responsabilidade - Fazenda Progresso</title>
<style>
  @page {
    size: A4;
    margin: 15mm 15mm 15mm 15mm;
  }
  body {
    font-family: Arial, Helvetica, sans-serif;
    color: #111827;
    background: #e2e8f0;
    margin: 0;
    padding: 20px 0;
    font-size: 13.5px;
    line-height: 1.55;
  }
  .actions-bar {
    max-width: 800px;
    margin: 0 auto 16px auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .btn-print {
    background: #006aa6;
    color: #fff;
    border: none;
    padding: 10px 22px;
    font-size: 14px;
    font-weight: 700;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0, 106, 166, 0.25);
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  .btn-print:hover {
    background: #004e7c;
  }
  .btn-back {
    background: #ffffff;
    color: #334155;
    border: 1px solid #cbd5e1;
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
  }
  .doc-sheet {
    background: #ffffff;
    max-width: 800px;
    margin: 0 auto 20px auto;
    padding: 45px 55px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    box-sizing: border-box;
    min-height: 1050px;
    position: relative;
    page-break-after: always;
  }
  .doc-sheet:last-child {
    page-break-after: avoid;
  }
  .header-logo {
    text-align: center;
    margin-bottom: 25px;
  }
  .header-logo img {
    height: 68px;
  }
  .header-logo .company-name {
    font-weight: 800;
    font-size: 15px;
    letter-spacing: 0.05em;
    color: #0f172a;
    margin-top: 6px;
    text-transform: uppercase;
  }
  .doc-title {
    text-align: center;
    font-weight: 800;
    font-size: 15px;
    margin: 28px 0;
    color: #0f172a;
  }
  .declaration-text {
    text-align: justify;
    margin-bottom: 25px;
    text-indent: 30px;
    font-size: 13.5px;
  }
  .doc-date {
    margin: 20px 0 25px 0;
    font-size: 13.5px;
  }
  .signature-area {
    text-align: center;
    margin: 20px 0 25px 0;
  }
  .signature-line {
    width: 320px;
    border-bottom: 1.5px solid #0f172a;
    margin: 0 auto 6px auto;
  }
  .signature-label {
    font-size: 13px;
    color: #334155;
  }
  .digital-badge {
    display: inline-block;
    padding: 6px 14px;
    background: #f0fdf4;
    border: 1px solid #86efac;
    border-radius: 8px;
    font-size: 11.5px;
    color: #166534;
    margin-top: 6px;
    text-align: center;
    line-height: 1.4;
  }
  .section-title {
    text-align: center;
    font-weight: 800;
    font-size: 14px;
    margin: 25px 0 15px 0;
    color: #0f172a;
    text-transform: uppercase;
  }
  ol.rules-list {
    margin: 0;
    padding-left: 20px;
    text-align: justify;
  }
  ol.rules-list > li {
    margin-bottom: 12px;
  }
  ol.rules-list ol.sub-rules {
    list-style-type: lower-alpha;
    padding-left: 20px;
    margin-top: 8px;
  }
  ol.rules-list ol.sub-rules li {
    margin-bottom: 10px;
  }
  .materials-box {
    margin-top: 25px;
    padding: 18px 24px;
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    font-size: 13.5px;
  }
  .materials-title {
    font-weight: 800;
    font-size: 14px;
    text-align: center;
    margin-bottom: 16px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .material-item {
    margin-bottom: 10px;
    line-height: 1.5;
  }
  .doc-footer {
    position: absolute;
    bottom: 25px;
    left: 55px;
    right: 55px;
    text-align: center;
    font-size: 11px;
    color: #475569;
    line-height: 1.4;
    border-top: 1px solid #e2e8f0;
    padding-top: 8px;
  }
  .doc-footer a {
    color: #006aa6;
    text-decoration: underline;
  }

  @media print {
    body {
      background: #ffffff;
      padding: 0;
    }
    .actions-bar {
      display: none !important;
    }
    .doc-sheet {
      box-shadow: none;
      padding: 0;
      margin: 0;
      min-height: auto;
    }
  }
</style>
</head>
<body>

<div class="actions-bar">
  <a href="comodato_view.php" class="btn-back">← Voltar para Comodatos</a>
  <div>
    <button onclick="window.print()" class="btn-print">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><path d="M6 14h12v8H6z"></path></svg>
      Imprimir Termo Oficial
    </button>
  </div>
</div>

<!-- FOLHA 1 -->
<div class="doc-sheet">
  <div class="header-logo">
    <img src="../public/logo-flowit.png" alt="Fazenda Progresso">
    <div class="company-name">FAZENDA PROGRESSO</div>
  </div>

  <div class="doc-title">
    Termo de responsabilidade pelo uso de recursos de informática.
  </div>

  <div class="declaration-text">
    Eu, <strong><?php echo $userName; ?></strong>, CPF número: <strong><?php echo $userCpf; ?></strong>, com contrato de trabalho estabelecido com o Fazenda Progresso Ltda., declaro que tenho conhecimento das condições de uso das ferramentas de informática disponibilizadas para o meu trabalho, nos termos das regras expostas no presente documento, com as quais concordo e comprometo-me seguir integralmente.
  </div>

  <div class="doc-date">
    <?php echo $data_formatada; ?>
  </div>

  <div class="signature-area">
    <?php if ($isSigned && !empty($signatureImg)): ?>
      <div style="margin-bottom:4px;">
        <img src="<?php echo $signatureImg; ?>" alt="Assinatura Digital" style="max-height:65px;display:inline-block;">
      </div>
      <div class="signature-line"></div>
      <div class="signature-label">(Assinatura Digital de <?php echo $userName; ?>)</div>
      <div class="digital-badge">
        ✓ Assinado Eletronicamente em <?php echo $signedAt; ?><br>
        IP: <?php echo $signerIp; ?> | Hash de Autenticidade: <?php echo substr($data['signature_token'], 0, 16); ?>...
      </div>
    <?php else: ?>
      <div style="height:45px;"></div>
      <div class="signature-line"></div>
      <div class="signature-label">(Assinatura)</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
        [Aguardando Assinatura Eletrônica do Colaborador]
      </div>
    <?php endif; ?>
  </div>

  <div class="section-title">
    CONDIÇÕES DE USO DE INTERNET E RECURSOS DE INFORMÁTICA
  </div>

  <ol class="rules-list">
    <li>
      Todos os equipamentos de informática, programas, vias ou condições de acesso à internet, inclusive correio eletrônico, são de propriedade da Fazenda Progresso Ltda., e são colocados à disposição dos COLABORADORES apenas e tão-somente como ferramentas de trabalho e para uso no desempenho de suas atividades profissionais, sendo vedado o seu uso nas seguintes condições:
      <ol class="sub-rules">
        <li>
          Para atividades ilegais ou que interfiram no trabalho alheio, para obter acesso não autorizado a qualquer outro computador, rede, banco de dados ou informações armazenadas eletronicamente, para reproduzir, instalar, alterar, copiar, distribuir programas (softwares), ou qualquer material que possa violar a legislação vigente, regulamentos internos ou código de ética e conduta da Fazenda Progresso Ltda.
        </li>
        <li>
          Cadastrar o e-mail profissional em listas de discussão, redes de relacionamento, fóruns e/ou formas similares de comunicação, bem como a manifestação de opinião em nome do Fazenda Progresso Ltda., sem expressa autorização do superior imediato responsável.
        </li>
      </ol>
    </li>
  </ol>

  <div class="doc-footer">
    <a href="mailto:fp@fazendaprogresso.com">fp@fazendaprogresso.com</a><br>
    Fazenda Progresso II, Rod. Ba-142, Km – 227, Mucugê – Bahia. CEP: 46.750-000<br>
    Fone/fax: (55 77) 3311-2191 a 2096
  </div>
</div>

<!-- FOLHA 2 -->
<div class="doc-sheet">
  <div class="header-logo">
    <img src="../public/logo-flowit.png" alt="Fazenda Progresso">
    <div class="company-name">FAZENDA PROGRESSO</div>
  </div>

  <ol class="rules-list" style="counter-reset: item 1;">
    <ol class="sub-rules" style="counter-reset: subitem 2;">
      <li style="list-style-type: none; margin-left: -20px; margin-bottom: 10px;">
        <strong>c)</strong> Desinstalação de programas (softwares), a alteração de configuração de equipamentos ou a desinstalação de quaisquer mecanismos de controle ou verificação que estejam instalados nos recursos que forem colocados à sua disposição para o exercício de suas atividades profissionais, sem prévia autorização da área de suporte.
      </li>
      <li style="list-style-type: none; margin-left: -20px; margin-bottom: 12px;">
        <strong>d)</strong> Abertura dos equipamentos de informática, bem como a remoção ou alteração de quaisquer partes de tais equipamentos, sem a devida autorização da área de suporte da Fazenda Progresso Ltda.
      </li>
    </ol>
    <li value="2">
      Os COLABORADORES devem atender integral e prontamente a todas as orientações e diretivas emanadas pela Fazenda Progresso Ltda., que tenham relação com o uso de equipamentos de informática, programas, vias ou condições de acesso à internet;
    </li>
    <li value="3">
      Os COLABORADORES devem empenhar-se no sentido de manter em boas condições todos os equipamentos de informática, programas, vias e condições de acesso à internet que lhes sejam fornecidos para o desempenho de suas atividades profissionais, devendo reportar de imediato qualquer tipo de problema técnico ou ocorrência anormal.
    </li>
  </ol>

  <div class="materials-box">
    <div class="materials-title">MATERIAIS ENTREGUES</div>

    <?php if ($contractType === 'notebook'): ?>
      <div class="material-item">
        <strong>01 Notebook:</strong> <?php echo $assetName ?: 'Dell Inspiron 15 3520'; ?><br>
        <strong>Modelo:</strong> <?php echo $assetModel ?: 'P112F'; ?><br>
        <strong>Etiqueta de Patrimônio / Serial:</strong> <?php echo $assetSerial; ?>
      </div>
      <div class="material-item" style="margin-top: 14px;">
        <strong>01 Adaptador CA/CC</strong><br>
        <?php if (!empty($accessories)): ?>
          <?php echo nl2br($accessories); ?>
        <?php else: ?>
          MODELO: LA65NS2-01<br>
          INPUT: 110V-240V ~ 1.6A 50-60 Hz<br>
          OUTPUT: 19,5V==3.34A
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="material-item">
        <strong>01 Smartphone:</strong> <?php echo $assetName ?: 'Samsung A26 5G'; ?><br>
        <strong>Etiqueta de Patrimônio / IMEI:</strong> <?php echo $assetSerial; ?><br>
        <strong>Modelo:</strong> <?php echo $assetModel ?: 'SM-A266M/DS'; ?>
      </div>
      <div class="material-item" style="margin-top: 14px;">
        <?php if (!empty($accessories)): ?>
          <?php echo nl2br($accessories); ?>
        <?php else: ?>
          <strong>01 Carregador</strong><br>
          Modelo: MC – 207L<br>
          INPUT: 100V-240V ~ 0.3A 50-60 Hz<br>
          OUTPUT: 5V==2.0A 15.0W<br><br>
          <strong>01 Cabo de Sincronização:</strong> USB – A / USB – C<br>
          <strong>01 Carregador de Parede</strong><br>
          <strong>01 Chip TIM e chave removedora de chip</strong>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="doc-footer">
    <a href="mailto:fp@fazendaprogresso.com">fp@fazendaprogresso.com</a><br>
    Fazenda Progresso II, Rod. Ba-142, Km – 227, Mucugê – Bahia. CEP: 46.750-000<br>
    Fone/fax: (55 77) 3311-2191 a 2096
  </div>
</div>

</body>
</html>
