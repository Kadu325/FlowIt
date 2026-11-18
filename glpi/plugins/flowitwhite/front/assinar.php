<?php
/**
 * FlowIT - Página Pública / Responsiva para Assinatura Eletrônica de Termo de Comodato
 * Fazenda Progresso Ltda.
 */

require_once __DIR__ . '/bootstrap.php';

global $DB;

$token = trim($_GET['token'] ?? '');

if (empty($token)) {
    die("Token de assinatura inválido ou não informado.");
}

$iterator = $DB->request([
    'FROM' => 'glpi_plugin_flowit_comodato',
    'WHERE' => ['signature_token' => $token]
]);

$data = null;
foreach ($iterator as $row) {
    $data = $row;
    break;
}

if (!$data) {
    die("Termo de comodato não encontrado para este link.");
}

$isSigned = ($data['signature_status'] === 'assinado');
$userName = htmlspecialchars($data['user_name'] ?: 'COLABORADOR', ENT_QUOTES, 'UTF-8');
$userCpf = htmlspecialchars($data['user_cpf'] ?: '-', ENT_QUOTES, 'UTF-8');
$contractType = $data['contract_type'];
$assetName = htmlspecialchars($data['asset_name'] ?: '', ENT_QUOTES, 'UTF-8');
$assetModel = htmlspecialchars($data['asset_model'] ?: '', ENT_QUOTES, 'UTF-8');
$assetSerial = htmlspecialchars($data['asset_serial'] ?: '', ENT_QUOTES, 'UTF-8');
$accessories = htmlspecialchars($data['accessories'] ?: '', ENT_QUOTES, 'UTF-8');
$assignedDate = date('d/m/Y', strtotime($data['assigned_date']));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>Assinatura de Termo de Comodato — Fazenda Progresso</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
:root{
  --bg:#f1f5f9;--surface:#ffffff;--line:#cbd5e1;--text:#0f172a;--muted:#64748b;
  --brand:#006aa6;--brand-dark:#004e7c;--ok:#16a34a;--ok-soft:#dcfce7;
  --shadow:0 10px 25px -5px rgba(0,0,0,0.1),0 8px 10px -6px rgba(0,0,0,0.05);
}
*{box-sizing:border-box}
body{
  margin:0;
  background:linear-gradient(180deg, #e2e8f0 0%, #f8fafc 100%);
  color:var(--text);
  font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
  min-height:100vh;
  padding:20px 14px;
  display:flex;
  justify-content:center;
  align-items:flex-start;
}
.sign-container{
  max-width:760px;
  width:100%;
  background:var(--surface);
  border-radius:18px;
  box-shadow:var(--shadow);
  border:1px solid rgba(255,255,255,0.7);
  overflow:hidden;
}
.sign-header{
  padding:26px 28px;
  border-bottom:1px solid var(--line);
  background:#fafbfc;
  text-align:center;
}
.sign-header img{
  height:58px;
  margin-bottom:8px;
}
.sign-header h1{
  font-size:18px;
  margin:4px 0 2px 0;
  color:#0f172a;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:0.02em;
}
.sign-header p{
  font-size:13px;
  color:var(--muted);
  margin:0;
}
.sign-body{
  padding:28px;
}
.alert-signed{
  background:var(--ok-soft);
  border:1.5px solid #86efac;
  border-radius:12px;
  padding:20px;
  display:flex;
  align-items:center;
  gap:16px;
  margin-bottom:24px;
}
.alert-signed i{
  font-size:36px;
  color:var(--ok);
}
.alert-signed h3{
  margin:0 0 4px 0;
  color:#15803d;
  font-size:16px;
}
.alert-signed p{
  margin:0;
  font-size:13px;
  color:#166534;
}

.doc-preview-box{
  background:#f8fafc;
  border:1.5px solid var(--line);
  border-radius:12px;
  padding:24px;
  margin-bottom:24px;
  font-size:13.5px;
  line-height:1.6;
}
.doc-preview-box h2{
  font-size:15px;
  font-weight:800;
  text-align:center;
  margin-top:0;
  margin-bottom:18px;
  color:#0f172a;
}
.doc-section-title{
  font-size:12.5px;
  font-weight:700;
  text-transform:uppercase;
  letter-spacing:0.04em;
  color:#334155;
  margin:20px 0 8px 0;
  border-bottom:1px solid var(--line);
  padding-bottom:4px;
}
.asset-summary-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:12px;
  background:#ffffff;
  border:1px solid var(--line);
  border-radius:10px;
  padding:14px 16px;
  margin-top:10px;
}
@media (max-width: 600px){
  .asset-summary-grid{grid-template-columns:1fr;}
}
.asset-summary-item .k{
  font-size:11px;
  font-weight:700;
  color:var(--muted);
  text-transform:uppercase;
}
.asset-summary-item .v{
  font-size:13.5px;
  font-weight:600;
  color:#0f172a;
}

/* Canvas de Assinatura */
.sign-canvas-section{
  background:#ffffff;
  border:2px dashed #94a3b8;
  border-radius:14px;
  padding:18px;
  text-align:center;
  margin-bottom:24px;
}
.sign-canvas-section.active{
  border-color:var(--brand);
}
.canvas-wrapper{
  position:relative;
  width:100%;
  max-width:520px;
  height:180px;
  margin:10px auto;
  background:#f8fafc;
  border:1px solid var(--line);
  border-radius:10px;
  touch-action:none;
}
canvas#signCanvas{
  width:100%;
  height:100%;
  display:block;
  cursor:crosshair;
}
.canvas-hint{
  position:absolute;
  inset:0;
  display:flex;
  align-items:center;
  justify-content:center;
  pointer-events:none;
  color:#94a3b8;
  font-size:14px;
  gap:8px;
}
.canvas-actions{
  display:flex;
  justify-content:center;
  gap:12px;
  margin-top:10px;
}
.btn{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:10px 20px;
  border-radius:9px;
  font-size:14px;
  font-weight:700;
  cursor:pointer;
  border:none;
  transition:all .2s ease;
}
.btn-primary{
  background:linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
  color:#fff;
  box-shadow:0 4px 12px rgba(0, 106, 166, 0.25);
}
.btn-primary:hover{
  background:linear-gradient(135deg, #0284c7 0%, var(--brand) 100%);
  transform:translateY(-1px);
}
.btn-outline{
  background:#fff;
  color:#475569;
  border:1.5px solid var(--line);
}
.btn-outline:hover{
  background:#f1f5f9;
}
.sign-legal-check{
  display:flex;
  align-items:flex-start;
  gap:10px;
  margin-bottom:20px;
  font-size:12.5px;
  color:#334155;
  line-height:1.45;
}
.sign-legal-check input{
  width:18px;
  height:18px;
  margin-top:2px;
  accent-color:var(--brand);
  cursor:pointer;
}
.sign-footer-info{
  text-align:center;
  font-size:11.5px;
  color:var(--muted);
  border-top:1px solid var(--line);
  padding:18px 24px;
  background:#fafbfc;
}
</style>
</head>
<body>

<div class="sign-container">
  <div class="sign-header">
    <img src="../public/logo-flowit.png" alt="Fazenda Progresso">
    <h1>Fazenda Progresso Ltda.</h1>
    <p>Portal Seguro de Assinatura Eletrônica de Termo de Comodato</p>
  </div>

  <div class="sign-body">
    <?php if ($isSigned): ?>
      <div class="alert-signed">
        <i class="ti ti-circle-check"></i>
        <div>
          <h3>Termo Assinado com Sucesso!</h3>
          <p>Este termo foi assinado eletronicamente em <strong><?php echo date('d/m/Y \à\s H:i', strtotime($data['signed_at'])); ?></strong> pelo titular <strong><?php echo $userName; ?></strong> (IP: <?php echo htmlspecialchars($data['signer_ip'] ?: 'Registrado'); ?>).</p>
        </div>
      </div>
    <?php endif; ?>

    <!-- Visualização Resumida do Termo -->
    <div class="doc-preview-box">
      <h2>Termo de Responsabilidade pelo Uso de Recursos de TI</h2>
      <p style="text-indent:20px;text-align:justify;">
        Eu, <strong><?php echo $userName; ?></strong>, portador do CPF <strong><?php echo $userCpf; ?></strong>, com contrato de trabalho estabelecido com o Fazenda Progresso Ltda., declaro que recebi o equipamento abaixo especificado em perfeitas condições de conservação e funcionamento, concordando plenamente com as normas corporativas e condições de uso de recursos de informática da empresa.
      </p>

      <div class="doc-section-title">Equipamento & Ativo Entregue</div>
      <div class="asset-summary-grid">
        <div class="asset-summary-item">
          <div class="k">Dispositivo / Descrição</div>
          <div class="v"><?php echo $assetName; ?></div>
        </div>
        <div class="asset-summary-item">
          <div class="k">Modelo do Fabricante</div>
          <div class="v"><?php echo $assetModel; ?></div>
        </div>
        <div class="asset-summary-item">
          <div class="k">Nº Série / IMEI / Patrimônio</div>
          <div class="v"><code><?php echo $assetSerial; ?></code></div>
        </div>
        <div class="asset-summary-item">
          <div class="k">Data da Entrega</div>
          <div class="v"><?php echo $assignedDate; ?></div>
        </div>
      </div>

      <div class="doc-section-title">Acessórios / Materiais Integrantes</div>
      <div style="background:#ffffff;border:1px solid var(--line);border-radius:10px;padding:12px 16px;white-space:pre-line;font-size:12.5px;color:#334155;">
        <?php echo $accessories; ?>
      </div>

      <div class="doc-section-title">Condições de Uso e Compromissos</div>
      <p style="font-size:12px;color:#475569;margin-bottom:0;text-align:justify;">
        Comprometo-me a utilizar as ferramentas exclusivamente para fins profissionais, zelar pela sua conservação, não desinstalar softwares homologados de segurança ou inventário, não permitir o uso por terceiros e comunicar imediatamente à área de suporte qualquer extravio, dano ou problema técnico.
      </p>
    </div>

    <?php if (!$isSigned): ?>
      <!-- ÁREA DE COLETA DE ASSINATURA -->
      <form id="signatureForm" onsubmit="submitSignature(event)">
        <div class="sign-canvas-section">
          <label style="font-weight:700;font-size:14px;color:#0f172a;display:block;margin-bottom:4px;">
            <i class="ti ti-signature"></i> Desenhe sua assinatura abaixo (com o dedo ou mouse)
          </label>
          <span style="font-size:12px;color:var(--muted);display:block;">Assine no retângulo abaixo de forma legível</span>

          <div class="canvas-wrapper" id="canvasWrapper">
            <canvas id="signCanvas"></canvas>
            <div class="canvas-hint" id="canvasHint">
              <i class="ti ti-pencil"></i> Clique ou toque aqui para assinar
            </div>
          </div>

          <div class="canvas-actions">
            <button type="button" class="btn btn-outline" onclick="clearCanvas()">
              <i class="ti ti-eraser"></i> Limpar Assinatura
            </button>
          </div>
        </div>

        <div class="sign-legal-check">
          <input type="checkbox" id="agreeTerms" required>
          <label for="agreeTerms">
            Declaro sob as penas da lei que li, compreendi e concordo integralmente com os termos deste comodato, e que a assinatura aposta acima pertence de fato a mim (<strong><?php echo $userName; ?></strong>, CPF: <?php echo $userCpf; ?>).
          </label>
        </div>

        <div style="text-align:center;">
          <button type="submit" id="btnSubmitSign" class="btn btn-primary" style="padding:14px 32px;font-size:15px;width:100%;max-width:380px;">
            <i class="ti ti-circle-check"></i> Confirmar & Assinar Termo
          </button>
        </div>
      </form>
    <?php else: ?>
      <!-- ASSINATURA JÁ REGISTRADA -->
      <div style="text-align:center;padding:16px 0;">
        <span style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;">Assinatura Registrada:</span>
        <div style="margin:12px auto;max-width:320px;padding:12px;background:#fff;border:1px solid var(--line);border-radius:10px;">
          <?php if (!empty($data['signature_image'])): ?>
            <img src="<?php echo $data['signature_image']; ?>" alt="Assinatura Eletrônica" style="max-height:80px;max-width:100%;">
          <?php else: ?>
            <em>Assinatura validada eletronicamente</em>
          <?php endif; ?>
        </div>
        <a href="comodato_doc.php?id=<?php echo $data['id']; ?>" target="_blank" class="btn btn-outline" style="margin-top:10px;">
          <i class="ti ti-file-text"></i> Visualizar Documento Completo
        </a>
      </div>
    <?php endif; ?>
  </div>

  <div class="sign-footer-info">
    Fazenda Progresso II, Rod. Ba-142, Km – 227, Mucugê – Bahia | CEP: 46.750-000<br>
    Identificador único de autenticidade: <code><?php echo substr($token, 0, 16); ?>...</code>
  </div>
</div>

<script>
const token = "<?php echo $token; ?>";
const isSigned = <?php echo $isSigned ? 'true' : 'false'; ?>;

let canvas, ctx, drawing = false, hasDrawn = false;

if (!isSigned) {
  canvas = document.getElementById('signCanvas');
  ctx = canvas.getContext('2d');

  function resizeCanvas() {
    const wrapper = document.getElementById('canvasWrapper');
    const rect = wrapper.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#004e7c';
  }

  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
    return {
      x: clientX - rect.left,
      y: clientY - rect.top
    };
  }

  function startDrawing(e) {
    drawing = true;
    hasDrawn = true;
    document.getElementById('canvasHint').style.display = 'none';
    const pos = getPos(e);
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
    e.preventDefault();
  }

  function draw(e) {
    if (!drawing) return;
    const pos = getPos(e);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
    e.preventDefault();
  }

  function stopDrawing() {
    drawing = false;
  }

  canvas.addEventListener('mousedown', startDrawing);
  canvas.addEventListener('mousemove', draw);
  window.addEventListener('mouseup', stopDrawing);

  canvas.addEventListener('touchstart', startDrawing, { passive: false });
  canvas.addEventListener('touchmove', draw, { passive: false });
  window.addEventListener('touchend', stopDrawing);
}

function clearCanvas() {
  if (!canvas) return;
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  hasDrawn = false;
  document.getElementById('canvasHint').style.display = 'flex';
}

async function submitSignature(e) {
  e.preventDefault();

  if (!hasDrawn) {
    alert('Por favor, desenhe sua assinatura no quadro antes de confirmar.');
    return;
  }

  const btn = document.getElementById('btnSubmitSign');
  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Processando assinatura...';

  const signatureImage = canvas.toDataURL('image/png');

  try {
    const formData = new FormData();
    formData.append('action', 'sign');
    formData.append('token', token);
    formData.append('signature_image', signatureImage);

    const res = await fetch('comodato_api.php', {
      method: 'POST',
      body: formData
    });

    const json = await res.json();
    if (json.success) {
      alert('Assinatura registrada com sucesso! O FlowIT foi atualizado.');
      window.location.reload();
    } else {
      alert('Erro: ' + (json.error || 'Falha ao assinar'));
      btn.disabled = false;
      btn.innerHTML = '<i class="ti ti-circle-check"></i> Confirmar & Assinar Termo';
    }
  } catch (err) {
    alert('Falha na comunicação com o servidor.');
    btn.disabled = false;
    btn.innerHTML = '<i class="ti ti-circle-check"></i> Confirmar & Assinar Termo';
  }
}
</script>

</body>
</html>
