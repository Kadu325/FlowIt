<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Solicitação de Compra</title>
<style>
:root{
  --bg:#f4f6f3; --surface:#ffffff; --surface-2:#f8faf7; --border:#dfe5dc; --border-strong:#c3ccbf;
  --text:#1c2a1f; --text-2:#56645a; --text-3:#7a877d;
  --brand:#1f6b3a; --brand-strong:#17542d; --brand-soft:#e5f1e8; --on-brand:#ffffff;
  --info:#1d5fa8; --info-soft:#e7f0fb;
  --warn:#8a5a00; --warn-soft:#fdf3dc; --warn-border:#e8c77a;
  --danger:#b3261e; --danger-soft:#fbe9e7;
  --ok:#1f6b3a;
  --focus:#2a7de1;
  --radius:10px; --radius-sm:6px;
  --shadow:0 1px 2px rgba(20,40,25,.06),0 1px 1px rgba(20,40,25,.04);
  --font:"Segoe UI",system-ui,-apple-system,Roboto,"Helvetica Neue",Arial,sans-serif;
}
@media (prefers-color-scheme: dark){
  :root:not([data-theme="light"]){
    --bg:#121714; --surface:#1a211c; --surface-2:#161c18; --border:#2c3630; --border-strong:#3c4840;
    --text:#e6ece7; --text-2:#a9b5ac; --text-3:#87948a;
    --brand:#5cb37a; --brand-strong:#79c693; --brand-soft:#1f3326; --on-brand:#0d1a11;
    --info:#7fb0ec; --info-soft:#1a2735;
    --warn:#e6b85c; --warn-soft:#2e2615; --warn-border:#6b5426;
    --danger:#f08a80; --danger-soft:#3a1d1a; --ok:#5cb37a; --focus:#7fb0ec;
    --shadow:none;
  }
}
:root[data-theme="dark"]{
  --bg:#121714; --surface:#1a211c; --surface-2:#161c18; --border:#2c3630; --border-strong:#3c4840;
  --text:#e6ece7; --text-2:#a9b5ac; --text-3:#87948a;
  --brand:#5cb37a; --brand-strong:#79c693; --brand-soft:#1f3326; --on-brand:#0d1a11;
  --info:#7fb0ec; --info-soft:#1a2735;
  --warn:#e6b85c; --warn-soft:#2e2615; --warn-border:#6b5426;
  --danger:#f08a80; --danger-soft:#3a1d1a; --ok:#5cb37a; --focus:#7fb0ec;
  --shadow:none;
}
*{box-sizing:border-box}
html,body{margin:0}
body{background:var(--bg);color:var(--text);font:14px/1.5 var(--font);-webkit-font-smoothing:antialiased}
button,input,select,textarea{font:inherit;color:inherit}
.num{font-variant-numeric:tabular-nums}

/* Top bar */
.topbar{background:var(--surface);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:20}
.topbar-in{max-width:1280px;margin:0 auto;padding:12px 24px;display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.crumbs{font-size:12px;color:var(--text-3)}
.title-row{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
h1{font-size:20px;line-height:1.3;margin:0;font-weight:650}
.badge{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;padding:2px 10px;border-radius:999px;border:1px solid var(--border-strong);color:var(--text-2);background:var(--surface-2)}
.badge::before{content:"";width:7px;height:7px;border-radius:50%;background:currentColor}
.badge.draft{color:var(--text-2)}
.badge.sent{color:var(--info);border-color:var(--info);background:var(--info-soft)}
.spacer{flex:1}
.reqno{font-size:12px;color:var(--text-2)}
.reqno b{color:var(--text)}

/* Stepper */
.stepper{max-width:1280px;margin:0 auto;padding:0 24px 12px;display:flex;gap:4px;overflow-x:auto}
.step{flex:1;min-width:120px;font-size:12px;color:var(--text-3);padding-top:8px;border-top:3px solid var(--border)}
.step b{display:block;color:var(--text-2);font-weight:600}
.step.done{border-color:var(--brand)}
.step.current{border-color:var(--brand)}
.step.current b{color:var(--brand)}

/* Layout */
.wrap{max-width:1280px;margin:0 auto;padding:24px;display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:24px;align-items:start}
@media (max-width:1000px){.wrap{grid-template-columns:1fr;padding:16px}}
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);margin-bottom:16px}
.card-h{display:flex;align-items:baseline;gap:10px;padding:16px 20px 0}
.card-h .n{font-size:12px;font-weight:700;color:var(--brand);background:var(--brand-soft);border-radius:6px;padding:1px 7px}
.card-h h2{font-size:16px;margin:0;font-weight:650}
.card-h p{margin:0;color:var(--text-3);font-size:12px}
.card-b{padding:16px 20px 20px}
.grid{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:16px}
.c3{grid-column:span 3}.c4{grid-column:span 4}.c5{grid-column:span 5}.c6{grid-column:span 6}.c8{grid-column:span 8}.c12{grid-column:span 12}
@media (max-width:720px){.c3,.c4,.c5,.c6,.c8{grid-column:span 12}}

/* Fields */
.field{display:flex;flex-direction:column;gap:6px;min-width:0}
.field label,.field .lbl{font-size:13px;font-weight:600;color:var(--text)}
.req{color:var(--danger);margin-left:2px}
.opt{color:var(--text-3);font-weight:400;font-size:12px;margin-left:4px}
.hint{font-size:12px;color:var(--text-3)}
.err{font-size:12px;color:var(--danger);display:none;align-items:center;gap:4px}
.field.invalid .err{display:flex}
.field.invalid input,.field.invalid select,.field.invalid textarea{border-color:var(--danger);background:var(--danger-soft)}
input[type=text],input[type=number],input[type=date],input[type=email],input[type=url],input[type=tel],select,textarea{
  width:100%;min-height:38px;padding:8px 10px;border:1px solid var(--border-strong);border-radius:var(--radius-sm);background:var(--surface);transition:border-color .15s}
textarea{min-height:76px;resize:vertical}
input[readonly]{background:var(--surface-2);color:var(--text-2);border-style:dashed}
input:focus-visible,select:focus-visible,textarea:focus-visible,button:focus-visible,.chk input:focus-visible{outline:2px solid var(--focus);outline-offset:2px;border-color:var(--focus)}
.ro{min-height:38px;padding:8px 10px;border:1px dashed var(--border-strong);border-radius:var(--radius-sm);background:var(--surface-2);color:var(--text-2)}
.chk{display:flex;gap:10px;align-items:flex-start;font-size:13px;cursor:pointer}
.chk input{width:18px;height:18px;margin:1px 0 0;accent-color:var(--brand);flex:none}
.seg{display:flex;flex-wrap:wrap;gap:8px}
.seg label{display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border-strong);border-radius:var(--radius-sm);cursor:pointer;font-size:13px;font-weight:500;background:var(--surface)}
.seg input{accent-color:var(--brand);margin:0}
.seg label:has(input:checked){border-color:var(--brand);background:var(--brand-soft);color:var(--text)}
.prio-dot{width:8px;height:8px;border-radius:50%}

/* Alerts */
.alert{display:flex;gap:10px;padding:10px 12px;border-radius:var(--radius-sm);font-size:13px;border:1px solid}
.alert svg{flex:none;margin-top:2px}
.alert.warn{background:var(--warn-soft);border-color:var(--warn-border);color:var(--text)}
.alert.warn svg{color:var(--warn)}
.alert.info{background:var(--info-soft);border-color:transparent;color:var(--text)}
.alert.info svg{color:var(--info)}
.alert.danger{background:var(--danger-soft);border-color:var(--danger);color:var(--text)}
.alert.danger svg{color:var(--danger)}
.hidden{display:none!important}
.cond{border-left:3px solid var(--brand);background:var(--surface-2);border-radius:0 var(--radius-sm) var(--radius-sm) 0;padding:14px 16px}
.cond-t{font-size:12px;font-weight:700;color:var(--brand);text-transform:uppercase;letter-spacing:.04em;margin-bottom:10px}

/* Items table */
.items{width:100%;border-collapse:separate;border-spacing:0}
.items th{font-size:12px;font-weight:600;color:var(--text-2);text-align:left;padding:0 8px 8px;white-space:nowrap}
.items td{padding:6px 8px;vertical-align:top;border-top:1px solid var(--border)}
.items td.r,.items th.r{text-align:right}
.items input,.items select{min-height:36px}
.item-sub{display:flex;gap:12px;flex-wrap:wrap;margin-top:8px}
.item-sub .field{flex:1;min-width:180px}
.stock{font-size:12px;margin-top:4px}
.stock.has{color:var(--warn);font-weight:600}
.stock.none{color:var(--text-3)}
.rowno{font-size:12px;color:var(--text-3);padding-top:16px!important;width:28px}
.items-scroll{overflow-x:auto;margin:0 -8px}
@media (max-width:860px){
  .items thead{display:none}
  .items,.items tbody,.items tr,.items td{display:block;width:100%}
  .items tr{border:1px solid var(--border);border-radius:var(--radius-sm);padding:8px;margin-bottom:12px}
  .items td{border:0;padding:4px 0}
  .items td[data-l]::before{content:attr(data-l);display:block;font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:4px}
  .items td.r{text-align:left}
  .items-scroll{margin:0}
}
.items-foot{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:12px;flex-wrap:wrap}
.total{font-size:13px;color:var(--text-2)}
.total b{font-size:18px;color:var(--text);margin-left:6px}

/* Buttons */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:8px 16px;border-radius:var(--radius-sm);border:1px solid var(--border-strong);background:var(--surface);font-weight:600;cursor:pointer;font-size:13px}
.btn:hover{background:var(--surface-2)}
.btn.primary{background:var(--brand);border-color:var(--brand);color:var(--on-brand)}
.btn.primary:hover{background:var(--brand-strong)}
.btn.ghost{border-color:transparent;background:transparent;color:var(--brand)}
.btn.ghost:hover{background:var(--brand-soft)}
.btn.icon{padding:0;width:36px;min-height:36px;color:var(--text-3)}
.btn.icon:hover{color:var(--danger);background:var(--danger-soft)}
.btn.block{width:100%}

/* Upload */
.drop{border:1.5px dashed var(--border-strong);border-radius:var(--radius);padding:20px;text-align:center;color:var(--text-2);background:var(--surface-2);cursor:pointer}
.drop:hover,.drop.over{border-color:var(--brand);background:var(--brand-soft)}
.drop b{color:var(--brand)}
.files{list-style:none;margin:12px 0 0;padding:0;display:flex;flex-direction:column;gap:8px}
.files li{display:grid;grid-template-columns:minmax(0,1fr) 220px 36px;gap:8px;align-items:center;padding:6px 8px;border:1px solid var(--border);border-radius:var(--radius-sm)}
.files .fn{font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.files .fs{font-size:12px;color:var(--text-3)}
@media (max-width:600px){.files li{grid-template-columns:minmax(0,1fr) 36px}.files li select{grid-column:1/-1;grid-row:2}}

/* Workflow section (read-only) */
.locked{position:relative}
.locked .lock-note{display:flex;gap:8px;align-items:center;font-size:12px;color:var(--text-3);margin-bottom:12px}
.wf{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
@media (max-width:720px){.wf{grid-template-columns:1fr}}
.wf .box{border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px 14px;background:var(--surface-2)}
.wf h3{margin:0 0 8px;font-size:13px}
.kv{display:grid;grid-template-columns:auto 1fr;gap:4px 12px;font-size:13px}
.kv dt{color:var(--text-3)} .kv dd{margin:0}

/* Summary */
aside{position:sticky;top:132px}
@media (max-width:1000px){aside{position:static}}
.sum .card-b{display:flex;flex-direction:column;gap:14px}
.sum-total{font-size:28px;font-weight:700;line-height:1.1}
.sum-l{font-size:12px;color:var(--text-3);text-transform:uppercase;letter-spacing:.04em;font-weight:600}
.tier{display:flex;flex-direction:column;gap:4px}
.tier-bar{display:grid;grid-template-columns:repeat(5,1fr);gap:3px;margin-top:4px}
.tier-bar span{height:6px;border-radius:3px;background:var(--border)}
.tier-bar span.on{background:var(--brand)}
.tier-bar span.cur{background:var(--brand);outline:2px solid var(--brand-soft)}
.tier-labels{display:grid;grid-template-columns:repeat(5,1fr);gap:3px;font-size:10.5px;color:var(--text-3)}
.progress{display:flex;flex-direction:column;gap:6px}
.pbar{height:6px;background:var(--border);border-radius:3px;overflow:hidden}
.pbar i{display:block;height:100%;background:var(--brand);width:0;transition:width .25s}
.todo{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:6px;font-size:13px}
.todo li{display:flex;gap:8px;align-items:center}
.todo li .dot{width:16px;height:16px;border-radius:50%;border:1.5px solid var(--border-strong);flex:none;display:grid;place-items:center}
.todo li.ok{color:var(--text-2)}
.todo li.ok .dot{background:var(--brand);border-color:var(--brand)}
.todo li.ok .dot::after{content:"";width:6px;height:3px;border:2px solid var(--on-brand);border-top:0;border-right:0;transform:rotate(-45deg) translate(1px,-1px)}
.todo a{color:inherit;text-decoration:none}
.todo a:hover{text-decoration:underline}
.actions{display:flex;flex-direction:column;gap:8px}
.saved{font-size:12px;color:var(--text-3);text-align:center}

/* Duplicates */
.dups{margin:8px 0 0;padding:0;list-style:none;font-size:13px}
.dups li{padding:6px 0;border-top:1px solid var(--border)}

/* Toast / dialog */
.toast{position:fixed;left:50%;bottom:24px;transform:translateX(-50%) translateY(20px);opacity:0;background:var(--text);color:var(--surface);padding:10px 16px;border-radius:8px;font-size:13px;transition:all .2s ease-out;z-index:50;max-width:calc(100% - 32px)}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
dialog{border:1px solid var(--border);border-radius:var(--radius);padding:0;max-width:480px;width:calc(100% - 32px);background:var(--surface);color:var(--text)}
dialog::backdrop{background:rgba(10,20,12,.45)}
.dlg-b{padding:24px}
.dlg-b h2{margin:0 0 8px;font-size:18px}
.dlg-f{padding:12px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px}
.big-no{font-size:28px;font-weight:700;color:var(--brand);margin:8px 0}
@media (prefers-reduced-motion:reduce){*{transition:none!important}}
</style>
</head>
<body>

<header class="topbar">
  <div class="topbar-in">
    <div>
      <div class="crumbs">Intranet › Compras › Nova solicitação</div>
      <div class="title-row">
        <h1>Solicitação de Compra</h1>
        <span class="badge draft" id="statusBadge">Rascunho</span>
      </div>
    </div>
    <div class="spacer"></div>
    <div class="reqno">Nº da solicitação: <b id="reqNo">gerado ao enviar</b> · Aberta em <b id="openedAt" class="num"></b></div>
  </div>
  <nav class="stepper" aria-label="Etapas da solicitação">
    <div class="step current" id="st1"><b>Rascunho</b>Você preenche</div>
    <div class="step" id="st2"><b>Em triagem</b>Compras confere</div>
    <div class="step" id="st3"><b>Aguardando gestor</b>Decisão do setor</div>
    <div class="step" id="st4"><b>Na fila de Compras</b>Cotação</div>
  </nav>
</header>

<main class="wrap">
<form id="form" novalidate>

  <!-- 1. Solicitante -->
  <section class="card" id="sec-solic" aria-labelledby="h1s">
    <div class="card-h"><span class="n">1</span><h2 id="h1s">Solicitante e área</h2></div>
    <div class="card-b grid">
      <div class="field c4"><span class="lbl">Solicitante</span><div class="ro" id="fSolic">Ana Ribeiro</div></div>
      <div class="field c4"><span class="lbl">E-mail</span><div class="ro" id="fEmail">ana.ribeiro@fazendaprogresso.com</div></div>
      <div class="field c4"><label for="fRamal">Ramal / telefone<span class="opt">opcional</span></label><input type="tel" id="fRamal" placeholder="(34) 0000-0000"></div>
      <div class="field c4" data-req><label for="fEmpresa">Empresa / unidade<span class="req">*</span></label>
        <select id="fEmpresa"><option value="">Selecione</option><option value="1" selected>1 · Fazenda Progresso — Matriz</option><option value="2">2 · Fazenda Progresso — Unidade II</option><option value="3">3 · Vinícola</option></select>
        <span class="err">Escolha a empresa.</span></div>
      <div class="field c4" data-req><label for="fArea">Área / setor<span class="req">*</span></label>
        <select id="fArea"><option value="TI" selected>Tecnologia da Informação</option></select>
        <span class="err">Escolha a área.</span></div>
      <div class="field c4"><span class="lbl">Gestor aprovador do setor</span><div class="ro" id="fGestor">—</div></div>
    </div>
  </section>

  <!-- 2. Classificação -->
  <section class="card" id="sec-class" aria-labelledby="h2s">
    <div class="card-h"><span class="n">2</span><h2 id="h2s">Classificação e prioridade</h2><p>O tipo abre os campos específicos.</p></div>
    <div class="card-b grid">
      <div class="field c12" data-req id="wTipo"><span class="lbl" id="lTipo">Tipo de solicitação<span class="req">*</span></span>
        <div class="seg" role="radiogroup" aria-labelledby="lTipo">
          <label><input type="radio" name="tipo" value="CONSUMO">Material de consumo</label>
          <label><input type="radio" name="tipo" value="INSUMO">Insumo agrícola</label>
          <label><input type="radio" name="tipo" value="SERVICO">Serviço</label>
          <label><input type="radio" name="tipo" value="TI" checked>Item de TI</label>
          <label><input type="radio" name="tipo" value="CURSO">Curso / palestra</label>
          <label><input type="radio" name="tipo" value="IMOB">Imobilizado</label>
        </div>
        <span class="err">Escolha o tipo de solicitação.</span></div>
      <div class="field c12" data-req><span class="lbl" id="lPrio">Prioridade<span class="req">*</span></span>
        <div class="seg" role="radiogroup" aria-labelledby="lPrio">
          <label><input type="radio" name="prio" value="1"><span class="prio-dot" style="background:var(--text-3)"></span>Baixa · mais de 10 dias</label>
          <label><input type="radio" name="prio" value="2" checked><span class="prio-dot" style="background:var(--info)"></span>Normal · até 10 dias</label>
          <label><input type="radio" name="prio" value="3"><span class="prio-dot" style="background:var(--warn)"></span>Alta · até 3 dias</label>
          <label><input type="radio" name="prio" value="4"><span class="prio-dot" style="background:var(--danger)"></span>Urgente · imediato</label>
        </div>
        <span class="hint">Tipo de compra no ERP: <b id="tipoCompra">Normal</b></span></div>
      <div class="field c12 hidden" id="wJustUrg" data-req><label for="fJustUrg">Justificativa da urgência<span class="req">*</span></label>
        <textarea id="fJustUrg" placeholder="O que acontece se o item não chegar no prazo? Ex.: colheitadeira parada no talhão 12."></textarea>
        <span class="hint">Mínimo de 30 caracteres. Urgência não dispensa registro nem aprovação.</span>
        <span class="err">Explique a urgência (mínimo 30 caracteres).</span></div>
    </div>
  </section>

  <!-- 3. Itens -->
  <section class="card" id="sec-itens" aria-labelledby="h3s">
    <div class="card-h"><span class="n">3</span><h2 id="h3s">Itens solicitados</h2><p>Um comprador deve conseguir cotar sem telefonar.</p></div>
    <div class="card-b">
      <div class="items-scroll">
      <table class="items" aria-describedby="h3s">
        <thead><tr><th></th><th style="min-width:240px">Produto / serviço <span class="req">*</span></th><th style="width:110px">Qtd. <span class="req">*</span></th><th style="width:110px">Unidade <span class="req">*</span></th><th class="r" style="width:140px">Vlr. unit. estimado <span class="req">*</span></th><th class="r" style="width:120px">Total</th><th style="width:44px"></th></tr></thead>
        <tbody id="itemsBody"></tbody>
      </table>
      </div>
      <div class="items-foot">
        <button type="button" class="btn ghost" id="addItem">
          <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Adicionar item</button>
        <div class="total">Total estimado <b class="num" id="grandTotal">R$ 0,00</b></div>
      </div>
      <div class="alert warn hidden" id="stockAlert" style="margin-top:12px" role="status">
        <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M8 1.5 15 14H1z" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M8 6v4M8 11.6v.4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        <div id="stockAlertTxt"></div>
      </div>
    </div>
  </section>

  <!-- 4. Finalidade -->
  <section class="card" id="sec-final" aria-labelledby="h4s">
    <div class="card-h"><span class="n">4</span><h2 id="h4s">Finalidade, prazo e entrega</h2></div>
    <div class="card-b grid">
      <div class="field c12" data-req><label for="fFinal">Finalidade / justificativa da necessidade<span class="req">*</span></label>
        <textarea id="fFinal" placeholder="Para que será usado e por que é necessário agora."></textarea>
        <span class="err">Descreva a finalidade (mínimo 20 caracteres).</span></div>
      <div class="field c4" data-req id="wData"><label for="fData">Data necessária<span class="req">*</span></label>
        <input type="date" id="fData"><span class="hint" id="dataHint"></span>
        <span class="err">Informe a data necessária.</span></div>
      <div class="field c4" data-req><label for="fLocal">Local de entrega / execução<span class="req">*</span></label>
        <select id="fLocal"><option value="">Selecione</option><option value="10" selected>Sede — Almoxarifado central</option><option value="20">Oficina mecânica</option><option value="30">Fazenda Unidade II — Almoxarifado</option><option value="40">Vinícola — Depósito</option></select>
        <span class="err">Escolha o local.</span></div>
      <div class="field c4"><label for="fReceb">Responsável pelo recebimento<span class="opt">opcional</span></label><input type="text" id="fReceb" value="Ana Ribeiro"></div>
      <div class="c12 alert warn hidden" id="dateAlert" role="status">
        <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><circle cx="8" cy="8" r="6.5" fill="none" stroke="currentColor" stroke-width="1.4"/><path d="M8 4.5V8l2.2 1.6" stroke="currentColor" stroke-width="1.4" fill="none" stroke-linecap="round"/></svg>
        <div id="dateAlertTxt"></div></div>
    </div>
  </section>

  <!-- 5. Orçamento -->
  <section class="card" id="sec-orc" aria-labelledby="h5s">
    <div class="card-h"><span class="n">5</span><h2 id="h5s">Informações orçamentárias</h2></div>
    <div class="card-b grid">
      <div class="field c4" data-req><label for="fCR">Centro de resultado<span class="req">*</span></label>
        <select id="fCR"><option value="">Selecione</option></select>
        <span class="hint">Mostra só os CRs permitidos ao seu usuário.</span>
        <span class="err">Escolha o centro de resultado.</span></div>
      <div class="field c4" data-req><label for="fNat">Natureza<span class="req">*</span></label>
        <select id="fNat"><option value="">Selecione</option><option value="2010100">2010100 · Material de consumo</option><option value="2010200">2010200 · Insumos agrícolas</option><option value="2020100">2020100 · Manutenção de máquinas</option><option value="2030100">2030100 · Serviços de terceiros</option><option value="2040100">2040100 · Tecnologia da informação</option><option value="2050100">2050100 · Treinamento</option><option value="1020100">1020100 · Imobilizado</option></select>
        <span class="hint" id="natHint">Sugerida pelo tipo; Compras pode ajustar.</span>
        <span class="err">Escolha a natureza.</span></div>
      <div class="field c4" id="wProj"><label for="fProj">Projeto<span class="req hidden" id="projReq">*</span><span class="opt" id="projOpt">opcional</span></label>
        <select id="fProj"><option value="">Nenhum</option><option value="101">101 · Safra 2026/27 — Soja</option><option value="102">102 · Safra 2026/27 — Café</option><option value="210">210 · Reforma oficina</option></select>
        <span class="err">Este CR exige projeto.</span></div>
      <div class="c12 grid" style="gap:16px">
        <div class="field c6"><span class="lbl">Saldo orçamentário do CR</span><div class="ro num" id="fSaldoOrc">—</div></div>
        <div class="field c6"><span class="lbl">Faixa de alçada prevista</span><div class="ro" id="fAlcada">—</div></div>
      </div>
      <div class="c12 alert danger hidden" id="budgetAlert" role="status">
        <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><circle cx="8" cy="8" r="6.5" fill="none" stroke="currentColor" stroke-width="1.4"/><path d="M8 4.5v4.2M8 10.8v.4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        <div><b>Orçamento comprometido.</b> O total estimado passa o saldo do centro de resultado; a solicitação seguirá marcada para aprovação formal do gestor.</div></div>
    </div>
  </section>

  <!-- 6. Fornecedor -->
  <section class="card" id="sec-forn" aria-labelledby="h6s">
    <div class="card-h"><span class="n">6</span><h2 id="h6s">Fornecedor sugerido</h2><p>É uma sugestão: a escolha e a negociação ficam com Compras.</p></div>
    <div class="card-b grid">
      <div class="field c5" id="wForn"><label for="fForn">Fornecedor sugerido<span class="req hidden" id="fornReq">*</span><span class="opt" id="fornOpt">opcional</span></label>
        <input type="text" id="fForn" list="dlForn" placeholder="Busque no cadastro ou digite o nome">
        <datalist id="dlForn"><option value="Agro Peças Triângulo Ltda"><option value="Casa do Produtor"><option value="TecnoInfo Sistemas"><option value="Instituto Rural de Capacitação"></datalist>
        <span class="err">Para curso ou palestra, indique o fornecedor.</span></div>
      <div class="field c4"><label for="fFornCt">Contato do fornecedor<span class="opt">opcional</span></label><input type="text" id="fFornCt" placeholder="Nome, e-mail ou telefone"></div>
      <div class="field c3"><label for="fContrato">Contrato vigente?<span class="opt">opcional</span></label>
        <select id="fContrato"><option>Não sei</option><option>Sim</option><option>Não</option></select></div>
    </div>
  </section>

  <!-- 7. Condicionais -->
  <section class="card hidden" id="sec-cond" aria-labelledby="h7s">
    <div class="card-h"><span class="n">7</span><h2 id="h7s">Informações específicas</h2></div>
    <div class="card-b">
      <div class="cond hidden" id="condTI">
        <div class="cond-t">Item de TI · validação técnica prévia</div>
        <div class="alert info" style="margin-bottom:12px">
          <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><circle cx="8" cy="8" r="6.5" fill="none" stroke="currentColor" stroke-width="1.4"/><path d="M8 7v4M8 4.8v.4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
          <div>A política exige que a TI valide a especificação <b>antes</b> da abertura. Sem estes dados, o envio fica bloqueado.</div></div>
        <div class="grid">
          <div class="field c4" data-req><label for="fChamado">Nº do chamado de validação<span class="req">*</span></label><input type="text" id="fChamado" placeholder="Ex.: GLPI 48213"><span class="err">Informe o chamado.</span></div>
          <div class="field c4" data-req><label for="fValPor">Validado por<span class="req">*</span></label>
            <select id="fValPor"><option value="">Selecione</option><option>Carlos Mendes (TI)</option><option>Juliana Prado (TI)</option></select><span class="err">Informe quem validou.</span></div>
          <div class="field c4" data-req><label for="fValData">Data da validação<span class="req">*</span></label><input type="date" id="fValData"><span class="err">A validação deve ser anterior ou igual a hoje.</span></div>
        </div>
      </div>
      <div class="cond hidden" id="condCurso">
        <div class="cond-t">Curso, palestra ou treinamento</div>
        <div class="grid">
          <div class="field c6" data-req><label for="fEvento">Nome do evento / tema<span class="req">*</span></label><input type="text" id="fEvento"><span class="err">Informe o tema.</span></div>
          <div class="field c3" data-req><label for="fEvIni">Início<span class="req">*</span></label><input type="date" id="fEvIni"><span class="err">Informe o início.</span></div>
          <div class="field c3" data-req><label for="fEvFim">Fim<span class="req">*</span></label><input type="date" id="fEvFim"><span class="err">Fim antes do início.</span></div>
          <div class="field c3" data-req><label for="fPart">Participantes<span class="req">*</span></label><input type="number" id="fPart" min="1" step="1"><span class="err">Mínimo 1.</span></div>
        </div>
      </div>
    </div>
  </section>

  <!-- 8. Origem -->
  <section class="card" id="sec-orig" aria-labelledby="h8s">
    <div class="card-h"><span class="n">8</span><h2 id="h8s">Origem e rastreabilidade</h2></div>
    <div class="card-b grid">
      <div class="field c4" data-req><label for="fCanal">Como a demanda chegou primeiro?<span class="req">*</span></label>
        <select id="fCanal"><option value="INTRANET" selected>Direto na intranet</option><option value="EMAIL">E-mail</option><option value="TEL">Telefone</option><option value="VERBAL">Verbal</option></select></div>
      <div class="field c4 hidden" id="wRefEmail" data-req><label for="fRefEmail">Referência do e-mail original<span class="req">*</span></label>
        <input type="text" id="fRefEmail" placeholder="Assunto e data, ex.: 'Peças trator' 06/10"><span class="err">Informe o assunto e a data do e-mail.</span></div>
      <div class="field c4"><label for="fRel">Solicitação relacionada<span class="opt">opcional</span></label><input type="text" id="fRel" placeholder="Nº de uma solicitação anterior"></div>
      <div class="c12" id="dupBox">
        <div class="alert warn hidden" id="dupAlert" role="status">
          <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><rect x="2" y="4" width="8" height="10" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.4"/><path d="M6 2h7a1 1 0 0 1 1 1v8" fill="none" stroke="currentColor" stroke-width="1.4"/></svg>
          <div><b>Possível duplicidade.</b> Você tem solicitação aberta com o mesmo produto nos últimos 30 dias:
            <ul class="dups" id="dupList"></ul></div></div>
      </div>
      <div class="field c12" data-req id="wDecl">
        <label class="chk"><input type="checkbox" id="fDecl"><span>Declaro que não há outra solicitação aberta para esta mesma necessidade.<span class="req">*</span></span></label>
        <span class="err">Confirme a declaração.</span></div>
    </div>
  </section>

  <!-- 9. Anexos -->
  <section class="card" id="sec-anex" aria-labelledby="h9s">
    <div class="card-h"><span class="n">9</span><h2 id="h9s">Anexos</h2><p id="anexHint">Opcional para este tipo.</p></div>
    <div class="card-b">
      <div class="field" id="wAnex">
        <div class="drop" id="drop" tabindex="0" role="button" aria-describedby="dropHint">
          <svg width="28" height="28" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V4m0 0-4 4m4-4 4 4M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <div>Arraste arquivos ou <b>clique para selecionar</b></div>
          <div class="hint" id="dropHint">PDF, JPG, PNG, XLSX, DOCX, MSG/EML · até 10 arquivos de 10 MB</div>
        </div>
        <input type="file" id="fFiles" multiple hidden accept=".pdf,.jpg,.jpeg,.png,.xlsx,.docx,.msg,.eml">
        <ul class="files" id="fileList"></ul>
        <span class="err" id="anexErr">Anexe a especificação ou o escopo.</span>
      </div>
      <div class="field" style="margin-top:16px"><label for="fLink">Link de referência<span class="opt">opcional</span></label><input type="url" id="fLink" placeholder="https://"></div>
    </div>
  </section>

  <!-- 10. Workflow -->
  <section class="card locked" id="sec-wf" aria-labelledby="h10s">
    <div class="card-h"><span class="n">10</span><h2 id="h10s">Aprovação e triagem</h2></div>
    <div class="card-b">
      <div class="lock-note"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true"><rect x="3" y="7" width="10" height="7" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.4"/><path d="M5.5 7V5a2.5 2.5 0 0 1 5 0v2" fill="none" stroke="currentColor" stroke-width="1.4"/></svg>Somente leitura. Preenchido por Compras e pelo gestor do setor depois do envio.</div>
      <div class="wf">
        <div class="box"><h3>Triagem de Compras</h3>
          <dl class="kv"><dt>Status</dt><dd id="wfStatus">Rascunho</dd><dt>Checklist</dt><dd>0 de 7 itens</dd><dt>Comprador</dt><dd>—</dd></dl></div>
        <div class="box"><h3>Decisão do gestor</h3>
          <dl class="kv"><dt>Gestor</dt><dd id="wfGestor">—</dd><dt>Decisão</dt><dd>Pendente</dd><dt>Data/hora</dt><dd>—</dd></dl></div>
      </div>
      <p class="hint" style="margin:12px 0 0">Próxima ação: <b id="wfNext">você — preencher e enviar</b></p>
    </div>
  </section>
</form>

<!-- Summary -->
<aside aria-label="Resumo da solicitação">
  <div class="card sum">
    <div class="card-b">
      <div><div class="sum-l">Total estimado</div><div class="sum-total num" id="sumTotal">R$ 0,00</div><div class="hint" id="sumItems">0 itens</div></div>
      <div class="tier">
        <div class="sum-l">Alçada da compra</div>
        <div id="sumTier" style="font-weight:600">—</div>
        <div class="tier-bar" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></div>
        <div class="tier-labels" aria-hidden="true"><span>5 mil</span><span>20 mil</span><span>100 mil</span><span>300 mil</span><span>+300 mil</span></div>
        <div class="hint">Vale depois da cotação. Agora decide o gestor do setor: <b id="sumGestor">—</b>.</div>
      </div>
      <div class="progress">
        <div style="display:flex;justify-content:space-between"><span class="sum-l">Preenchimento</span><span class="hint num" id="progTxt">0%</span></div>
        <div class="pbar"><i id="progBar"></i></div>
        <ul class="todo" id="todo"></ul>
      </div>
      <div class="actions">
        <button type="button" class="btn primary block" id="btnSend">Enviar para triagem</button>
        <button type="button" class="btn block" id="btnDraft">Salvar rascunho</button>
        <div class="saved" id="savedAt">Rascunho não salvo</div>
      </div>
    </div>
  </div>
</aside>
</main>

<dialog id="dlg" aria-labelledby="dlgT">
  <div class="dlg-b">
    <h2 id="dlgT">Solicitação enviada</h2>
    <div class="big-no num" id="dlgNo"></div>
    <p style="margin:0 0 8px">Ela está <b>em triagem</b> pela equipe de Compras. Depois segue para o gestor <b id="dlgGestor"></b>.</p>
    <p class="hint" style="margin:0" id="dlgEmail"></p>
  </div>
  <div class="dlg-f"><button class="btn" id="dlgCopy">Copiar número</button><button class="btn primary" id="dlgClose">Fechar</button></div>
</dialog>
<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script>
(() => {
  // ===== Dados de exemplo (substituir por chamadas à API do Sankhya) =====
  const PRODUTOS = [
  {
    "cod": 30550,
    "desc": "Notebook Corporativo 14\" i5 16GB SSD 512GB",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 5200
  },
  {
    "cod": 30551,
    "desc": "Notebook Alta Performance 15.6\" i7 32GB SSD 1TB",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 8400
  },
  {
    "cod": 30552,
    "desc": "Desktop Corporativo i5 16GB SSD 256GB",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 3900
  },
  {
    "cod": 30553,
    "desc": "Monitor LED 24\" IPS Full HD HDMI/DisplayPort",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 850
  },
  {
    "cod": 30554,
    "desc": "Monitor LED 27\" IPS Full HD / 2K",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 1250
  },
  {
    "cod": 30555,
    "desc": "Teclado e Mouse sem Fio USB",
    "un": [
      "KIT"
    ],
    "saldo": 0,
    "vlr": 160
  },
  {
    "cod": 30556,
    "desc": "Headset USB Profissional com Cancelamento de Ruído",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 220
  },
  {
    "cod": 30557,
    "desc": "Dock Station USB-C Dual Display HDMI/Rede",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 750
  },
  {
    "cod": 30558,
    "desc": "Nobreak Senoidal 1500VA Bivolt",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 1850
  },
  {
    "cod": 30559,
    "desc": "Nobreak 600VA Monovolt",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 420
  },
  {
    "cod": 30560,
    "desc": "Switch Gerenciável 24 Portas Gigabit PoE",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 3200
  },
  {
    "cod": 30561,
    "desc": "Ponto de Acesso Wi-Fi 6 Corporativo (Access Point)",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 1450
  },
  {
    "cod": 30562,
    "desc": "Cabo de Rede Patch Cord UTP Cat6 2.5m",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 18
  },
  {
    "cod": 30563,
    "desc": "Cabo de Rede Caixa Cat6 305m",
    "un": [
      "CX"
    ],
    "saldo": 0,
    "vlr": 780
  },
  {
    "cod": 30564,
    "desc": "Patch Panel 24 Portas Cat6 Carregado",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 310
  },
  {
    "cod": 30565,
    "desc": "Servidor Rack 2U Dual Xeon 64GB SAS",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 38000
  },
  {
    "cod": 30566,
    "desc": "Disco SSD Enterprise 960GB SATA/SAS",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 1600
  },
  {
    "cod": 30567,
    "desc": "Impressora Laser Multifuncional Monocromática Rede",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 2400
  },
  {
    "cod": 30568,
    "desc": "Toner Compatível / Original Preto Alto Rendimento",
    "un": [
      "UN"
    ],
    "saldo": 0,
    "vlr": 195
  },
  {
    "cod": 30569,
    "desc": "Licença Microsoft 365 Business Standard (Anual)",
    "un": [
      "LIC"
    ],
    "saldo": 0,
    "vlr": 890
  },
  {
    "cod": 30570,
    "desc": "Licença Antivírus / Endpoint Security Corporativo",
    "un": [
      "LIC"
    ],
    "saldo": 0,
    "vlr": 140
  },
  {
    "cod": 30571,
    "desc": "Serviço de Cabeamento Estruturado e Fibra Óptica",
    "un": [
      "SV",
      "H"
    ],
    "saldo": 0,
    "vlr": 0
  },
  {
    "cod": 30572,
    "desc": "Serviço de Consultoria / Suporte Especializado TI",
    "un": [
      "SV",
      "H"
    ],
    "saldo": 0,
    "vlr": 0
  },
  {
    "cod": 30573,
    "desc": "Serviço de Instalação e Manutenção de Câmeras CFTV",
    "un": [
      "SV"
    ],
    "saldo": 0,
    "vlr": 0
  },
  {
    "cod": 30574,
    "desc": "Link de Internet Dedicado / Fibra Óptica (Instalação)",
    "un": [
      "SV"
    ],
    "saldo": 0,
    "vlr": 0
  }
];
  const CRS = [
    {cod:'110100', nome:'Manutenção mecânica', area:'MAN', gestor:'Roberto Lima', saldo:38000, proj:false},
    {cod:'110200', nome:'Oficina — frota', area:'MAN', gestor:'Roberto Lima', saldo:12500, proj:true},
    {cod:'210100', nome:'Produção agrícola — grãos', area:'AGR', gestor:'Marina Costa', saldo:420000, proj:true},
    {cod:'310100', nome:'Tecnologia da informação', area:'TI', gestor:'Paulo Teixeira', saldo:61000, proj:false},
    {cod:'410100', nome:'Administrativo geral', area:'ADM', gestor:'Luciana Alves', saldo:9000, proj:false},
    {cod:'510100', nome:'Recursos humanos', area:'RH', gestor:'Fernanda Rocha', saldo:24000, proj:false}
  ];
  const GESTOR_AREA = {MAN:'Roberto Lima', AGR:'Marina Costa', TI:'Paulo Teixeira', ADM:'Luciana Alves', RH:'Fernanda Rocha'};
  const NAT_SUG = {CONSUMO:'2010100', INSUMO:'2010200', SERVICO:'2030100', TI:'2040100', CURSO:'2050100', IMOB:'1020100'};
  const ABERTAS = [{no:'48.211', cod:10231, data:'29/09/2026', status:'Em triagem'}];
  const PRAZO = {1:11, 2:10, 3:3, 4:0};
  const PRIO_NOME = {1:'Baixa', 2:'Normal', 3:'Alta', 4:'Urgente'};
  const ALCADAS = [
    {max:5000, quem:'Coordenador de Compras'},
    {max:20000, quem:'Gestor do centro de resultado'},
    {max:100000, quem:'Diretor da área'},
    {max:300000, quem:'Diretoria Financeira'},
    {max:Infinity, quem:'CEO'}
  ];

  const $ = s => document.querySelector(s);
  const $$ = s => [...document.querySelectorAll(s)];
  const brl = v => v.toLocaleString('pt-BR',{style:'currency',currency:'BRL'});
  const today = new Date(); today.setHours(0,0,0,0);
  const iso = d => new Date(d.getTime()-d.getTimezoneOffset()*6e4).toISOString().slice(0,10);
  const addDays = (d,n) => { const x = new Date(d); x.setDate(x.getDate()+n); return x; };
  const fmtBR = d => d.toLocaleDateString('pt-BR');

  $('#openedAt').textContent = fmtBR(today);
  let sent = false, tried = false;

  // ===== Itens =====
  let seq = 0;
  const prodOptions = PRODUTOS.map(p => `<option value="${p.cod}">${p.cod} · ${p.desc}</option>`).join('');
  function addItem(){
    seq++;
    const tr = document.createElement('tr'); tr.dataset.id = seq;
    tr.innerHTML = `
      <td class="rowno num">${$('#itemsBody').children.length+1}</td>
      <td data-l="Produto / serviço *">
        <div class="field" data-req><select class="iProd" aria-label="Produto ou serviço"><option value="">Busque por código ou descrição</option>${prodOptions}<option value="0">Item não encontrado no cadastro</option></select>
        <span class="err">Escolha o produto.</span></div>
        <div class="stock none"></div>
        <div class="item-sub">
          <div class="field iDescW hidden" data-req><label>Descrição complementar<span class="req">*</span></label><input type="text" class="iDesc" maxlength="250" placeholder="Descreva o item sem ambiguidade"><span class="err">Descreva o item.</span></div>
          <div class="field iEspW hidden"><label>Especificação técnica<span class="req iEspReq hidden">*</span></label><textarea class="iEsp" rows="2" placeholder="Medidas, normas, compatibilidade, escopo"></textarea><span class="err">Informe a especificação.</span></div>
          <div class="field iMaqW hidden" data-req><label>Máquina / equipamento<span class="req">*</span></label><select class="iMaq"><option value="">Selecione</option><option>TR-014 · Trator JD 6110J</option><option>CO-003 · Colheitadeira S760</option><option>PV-021 · Pulverizador 3030</option></select><span class="err">Informe a máquina.</span></div>
          <div class="field" style="max-width:220px"><label>Marca / modelo ref.<span class="opt">opcional</span></label><input type="text" class="iMarca"></div>
        </div>
      </td>
      <td data-l="Quantidade *"><div class="field" data-req><input type="number" class="iQtd num" min="0" step="any" inputmode="decimal" aria-label="Quantidade"><span class="err">Maior que zero.</span></div></td>
      <td data-l="Unidade *"><div class="field" data-req><select class="iUn" aria-label="Unidade"><option value="">—</option></select><span class="err">Escolha.</span></div></td>
      <td data-l="Vlr. unitário estimado *" class="r"><div class="field" data-req><input type="number" class="iVlr num" min="0" step="0.01" inputmode="decimal" style="text-align:right" aria-label="Valor unitário estimado"><span class="err">Informe a estimativa.</span></div></td>
      <td data-l="Total" class="r num iTot" style="padding-top:14px">R$ 0,00</td>
      <td><button type="button" class="btn icon iDel" aria-label="Remover item"><svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M3 4h10M6.5 4V2.5h3V4M4.5 4l.6 9.5h5.8l.6-9.5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg></button></td>`;
    $('#itemsBody').appendChild(tr);
    tr.querySelector('.iDel').onclick = () => { if ($('#itemsBody').children.length > 1){ tr.remove(); renumber(); update(); } else toast('A solicitação precisa de pelo menos 1 item.'); };
    tr.querySelector('.iProd').onchange = e => { onProd(tr); update(); };
    applyItemRules(tr);
    return tr;
  }
  function renumber(){ $$('#itemsBody tr').forEach((tr,i)=> tr.querySelector('.rowno').textContent = i+1); }
  function onProd(tr){
    const cod = +tr.querySelector('.iProd').value;
    const p = PRODUTOS.find(x => x.cod === cod);
    const un = tr.querySelector('.iUn');
    un.innerHTML = p ? p.un.map(u=>`<option>${u}</option>`).join('') : '<option value="">—</option><option>UN</option><option>KG</option><option>L</option><option>M</option><option>SV</option>';
    if (p && p.vlr && !tr.querySelector('.iVlr').value) tr.querySelector('.iVlr').value = p.vlr;
    const st = tr.querySelector('.stock');
    if (p && p.saldo > 0){ st.className='stock has'; st.textContent = `Saldo em estoque: ${p.saldo} ${p.un[0]} no almoxarifado`; }
    else if (p){ st.className='stock none'; st.textContent = 'Sem saldo em estoque'; }
    else { st.className='stock none'; st.textContent=''; }
    applyItemRules(tr);
  }
  function applyItemRules(tr){
    const tipo = getTipo();
    const cod = tr.querySelector('.iProd').value;
    const naoCad = cod === '0';
    const generico = cod === '50001';
    tr.querySelector('.iDescW').classList.toggle('hidden', !(naoCad || generico));
    const espObrig = ['TI','SERVICO','IMOB'].includes(tipo) || naoCad;
    tr.querySelector('.iEspW').classList.toggle('hidden', !(espObrig || tipo));
    tr.querySelector('.iEspReq').classList.toggle('hidden', !espObrig);
    tr.querySelector('.iEspW').toggleAttribute('data-req', espObrig);
    const peca = $('#fArea').value === 'MAN' && ['CONSUMO','SERVICO'].includes(tipo);
    tr.querySelector('.iMaqW').classList.toggle('hidden', !peca);
  }
  $('#addItem').onclick = () => { const tr = addItem(); tr.querySelector('.iProd').focus(); update(); };

  // ===== Helpers =====
  function getTipo(){ const r = document.querySelector('input[name=tipo]:checked'); return r ? r.value : ''; }
  function getPrio(){ return +document.querySelector('input[name=prio]:checked').value; }
  function total(){
    let t = 0, n = 0;
    $$('#itemsBody tr').forEach(tr => {
      const q = parseFloat(tr.querySelector('.iQtd').value)||0, v = parseFloat(tr.querySelector('.iVlr').value)||0;
      tr.querySelector('.iTot').textContent = brl(q*v); t += q*v; if (tr.querySelector('.iProd').value !== '') n++;
    });
    return {t, n};
  }
  function fillCR(){
    const area = $('#fArea').value, cur = $('#fCR').value;
    $('#fCR').innerHTML = '<option value="">Selecione</option>' + CRS.filter(c => !area || c.area === area).map(c=>`<option value="${c.cod}">${c.cod} · ${c.nome}</option>`).join('');
    if (cur && [...$('#fCR').options].some(o=>o.value===cur)) $('#fCR').value = cur;
    else { const first = CRS.find(c=>c.area===area); if (first) $('#fCR').value = first.cod; }
  }

  // ===== Regras de tela =====
  function update(){
    const tipo = getTipo(), prio = getPrio();
    const gestor = GESTOR_AREA[$('#fArea').value] || '—';
    $('#fGestor').textContent = gestor; $('#sumGestor').textContent = gestor; $('#wfGestor').textContent = gestor;

    // prioridade
    $('#tipoCompra').textContent = prio === 4 ? 'Urgente' : 'Normal';
    $('#wJustUrg').classList.toggle('hidden', prio < 3);
    $('#wJustUrg').toggleAttribute('data-req', prio >= 3);

    // data mínima pela prioridade
    const min = addDays(today, PRAZO[prio]);
    $('#dataHint').textContent = `Prazo da prioridade ${PRIO_NOME[prio]}: a partir de ${fmtBR(min)}`;
    const dv = $('#fData').value ? new Date($('#fData').value+'T00:00') : null;
    const early = dv && dv < min;
    $('#dateAlert').classList.toggle('hidden', !early);
    if (early) $('#dateAlertTxt').innerHTML = `<b>Prazo curto para a prioridade ${PRIO_NOME[prio]}.</b> Suba a prioridade ou ajuste a data para ${fmtBR(min)} ou depois.`;

    // condicionais
    $('#sec-cond').classList.toggle('hidden', !(tipo === 'TI' || tipo === 'CURSO'));
    $('#condTI').classList.toggle('hidden', tipo !== 'TI');
    $('#condCurso').classList.toggle('hidden', tipo !== 'CURSO');
    $('#wData').classList.toggle('hidden', tipo === 'CURSO');
    $('#wData').toggleAttribute('data-req', tipo !== 'CURSO');
    const fornObrig = tipo === 'CURSO';
    $('#fornReq').classList.toggle('hidden', !fornObrig); $('#fornOpt').classList.toggle('hidden', fornObrig);
    $('#wForn').toggleAttribute('data-req', fornObrig);
    const anexObrig = ['TI','SERVICO','IMOB'].includes(tipo);
    $('#anexHint').textContent = anexObrig ? (tipo==='TI' ? 'Obrigatório: evidência da validação de TI.' : 'Obrigatório: especificação ou escopo.') : 'Opcional para este tipo.';
    $('#wAnex').toggleAttribute('data-req', anexObrig);
    $('#anexErr').textContent = tipo==='TI' ? 'Anexe a evidência da validação de TI.' : 'Anexe a especificação ou o escopo.';
    $$('#itemsBody tr').forEach(applyItemRules);

    // canal
    $('#wRefEmail').classList.toggle('hidden', $('#fCanal').value !== 'EMAIL');
    $('#wRefEmail').toggleAttribute('data-req', $('#fCanal').value === 'EMAIL');

    // CR, projeto, orçamento
    const cr = CRS.find(c => c.cod === $('#fCR').value);
    $('#projReq').classList.toggle('hidden', !(cr && cr.proj)); $('#projOpt').classList.toggle('hidden', !!(cr && cr.proj));
    $('#wProj').toggleAttribute('data-req', !!(cr && cr.proj));
    const {t, n} = total();
    $('#fSaldoOrc').textContent = cr ? brl(cr.saldo) : '—';
    $('#budgetAlert').classList.toggle('hidden', !(cr && t > cr.saldo));

    // alçada
    const ti = ALCADAS.findIndex(a => t <= a.max);
    $('#grandTotal').textContent = brl(t); $('#sumTotal').textContent = brl(t);
    $('#sumItems').textContent = `${n} ${n===1?'item':'itens'}`;
    const tierTxt = t > 0 ? `${ALCADAS[ti].quem}` : '—';
    $('#fAlcada').textContent = t > 0 ? `${tierTxt} (${['até R$ 5 mil','R$ 5 a 20 mil','R$ 20 a 100 mil','R$ 100 a 300 mil','acima de R$ 300 mil'][ti]})` : '—';
    $('#sumTier').textContent = tierTxt;
    $$('.tier-bar span').forEach((s,i) => { s.className = t>0 && i<ti ? 'on' : (t>0 && i===ti ? 'cur' : ''); });

    // estoque
    const comSaldo = $$('#itemsBody tr').map(tr => PRODUTOS.find(p=>p.cod===+tr.querySelector('.iProd').value)).filter(p => p && p.saldo>0);
    $('#stockAlert').classList.toggle('hidden', !comSaldo.length);
    if (comSaldo.length) $('#stockAlertTxt').innerHTML = `<b>Há saldo em estoque</b> para ${comSaldo.map(p=>p.desc).join(', ')}. Confira com o almoxarifado se uma requisição de estoque resolve antes de comprar.`;

    // duplicidade
    const dups = ABERTAS.filter(a => $$('#itemsBody .iProd').some(s => +s.value === a.cod));
    $('#dupAlert').classList.toggle('hidden', !dups.length);
    $('#dupList').innerHTML = dups.map(d => `<li>Nº <b class="num">${d.no}</b> · aberta em ${d.data} · ${d.status}</li>`).join('');

    if (tried) validate(false);
    progress();
  }

  // ===== Validação =====
  function isVisible(el){ return !!(el.offsetParent || el.getClientRects().length); }
  function checkField(w){
    if (!w.hasAttribute('data-req') || w.closest('.hidden')) return true;
    let ok = true;
    const ctrl = w.querySelector('input:not([type=radio]):not([type=checkbox]):not([type=file]),select,textarea');
    if (w.id === 'wTipo') ok = !!getTipo();
    else if (w.id === 'wDecl') ok = $('#fDecl').checked;
    else if (w.id === 'wAnex') ok = files.length > 0;
    else if (ctrl){
      const v = ctrl.value.trim();
      ok = v !== '';
      if (ctrl.id === 'fFinal') ok = v.length >= 20;
      if (ctrl.id === 'fJustUrg') ok = v.length >= 30;
      if (ctrl.classList.contains('iQtd') || ctrl.id === 'fPart') ok = parseFloat(v) > 0;
      if (ctrl.classList.contains('iVlr')) ok = v !== '' && parseFloat(v) >= 0;
      if (ctrl.id === 'fValData' && v) ok = new Date(v+'T00:00') <= today;
      if (ctrl.id === 'fEvFim' && v && $('#fEvIni').value) ok = v >= $('#fEvIni').value;
    }
    return ok;
  }
  function validate(mark){
    let first = null;
    $$('[data-req]').forEach(w => {
      const ok = checkField(w);
      w.classList.toggle('invalid', !ok);
      if (!ok && !first) first = w;
    });
    return first;
  }

  // ===== Progresso / checklist lateral =====
  const SECS = [
    ['#sec-solic','Solicitante e área'], ['#sec-class','Tipo e prioridade'], ['#sec-itens','Itens'],
    ['#sec-final','Finalidade e prazo'], ['#sec-orc','Orçamento'], ['#sec-forn','Fornecedor'],
    ['#sec-cond','Informações específicas'], ['#sec-orig','Origem'], ['#sec-anex','Anexos']
  ];
  function progress(){
    const reqs = $$('[data-req]').filter(w => !w.closest('.hidden'));
    const done = reqs.filter(checkField).length;
    const pct = reqs.length ? Math.round(done/reqs.length*100) : 0;
    $('#progBar').style.width = pct+'%'; $('#progTxt').textContent = pct+'%';
    $('#todo').innerHTML = SECS.filter(([s]) => !$(s).classList.contains('hidden')).map(([s,n]) => {
      const rs = $$(s+' [data-req]').filter(w => !w.closest('.hidden'));
      const ok = rs.every(checkField);
      return `<li class="${ok?'ok':''}"><span class="dot"></span><a href="${s}">${n}</a></li>`;
    }).join('');
  }

  // ===== Anexos =====
  let files = [];
  const TIPOS_ANEXO = ['Especificação','Foto','Orçamento prévio','Validação de TI','E-mail original','Evidência de aprovação','Outro'];
  function renderFiles(){
    $('#fileList').innerHTML = files.map((f,i) => `<li><div><div class="fn">${f.name.replace(/</g,'&lt;')}</div><div class="fs num">${(f.size/1024/1024).toFixed(2)} MB</div></div>
      <select data-i="${i}" aria-label="Tipo de anexo">${TIPOS_ANEXO.map(t=>`<option ${f.tipo===t?'selected':''}>${t}</option>`).join('')}</select>
      <button type="button" class="btn icon" data-del="${i}" aria-label="Remover ${f.name.replace(/"/g,'')}"><svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button></li>`).join('');
    $$('#fileList [data-del]').forEach(b => b.onclick = () => { files.splice(+b.dataset.del,1); renderFiles(); update(); });
    $$('#fileList select').forEach(s => s.onchange = () => files[+s.dataset.i].tipo = s.value);
  }
  function addFiles(list){
    for (const f of list){
      if (files.length >= 10){ toast('Limite de 10 arquivos.'); break; }
      if (f.size > 10*1024*1024){ toast(`${f.name} passa de 10 MB.`); continue; }
      const tipo = getTipo()==='TI' ? 'Validação de TI' : (/\.(jpe?g|png)$/i.test(f.name) ? 'Foto' : (/\.(msg|eml)$/i.test(f.name) ? 'E-mail original' : 'Especificação'));
      files.push({name:f.name, size:f.size, tipo});
    }
    renderFiles(); update();
  }
  const drop = $('#drop');
  drop.onclick = () => $('#fFiles').click();
  drop.onkeydown = e => { if (e.key==='Enter'||e.key===' '){ e.preventDefault(); $('#fFiles').click(); } };
  $('#fFiles').onchange = e => { addFiles(e.target.files); e.target.value=''; };
  ['dragenter','dragover'].forEach(ev => drop.addEventListener(ev, e => { e.preventDefault(); drop.classList.add('over'); }));
  ['dragleave','drop'].forEach(ev => drop.addEventListener(ev, e => { e.preventDefault(); drop.classList.remove('over'); }));
  drop.addEventListener('drop', e => addFiles(e.dataTransfer.files));

  // ===== Eventos =====
  $('#fArea').onchange = () => { fillCR(); update(); };
  $$('input[name=tipo]').forEach(r => r.onchange = () => {
    const sug = NAT_SUG[getTipo()]; if (sug) { $('#fNat').value = sug; $('#natHint').textContent = 'Sugerida pelo tipo; Compras pode ajustar.'; }
    update();
  });
  $('#form').addEventListener('input', update);
  $('#form').addEventListener('change', update);

  let toastT;
  function toast(msg){ const t = $('#toast'); t.textContent = msg; t.classList.add('show'); clearTimeout(toastT); toastT = setTimeout(()=>t.classList.remove('show'), 3200); }

  $('#btnDraft').onclick = () => {
    const h = new Date().toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'});
    $('#savedAt').textContent = `Rascunho salvo às ${h} · ainda sem número no ERP`;
    toast('Rascunho salvo.');
  };

  $('#btnSend').onclick = () => {
    if (sent) return;
    tried = true;
    const first = validate(true);
    progress();
    if (first){
      const n = $$('[data-req].invalid').length;
      toast(`${n} ${n===1?'campo precisa':'campos precisam'} de atenção.`);
      first.scrollIntoView({behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto':'smooth', block:'center'});
      const c = first.querySelector('input,select,textarea'); if (c) setTimeout(()=>c.focus({preventScroll:true}), 300);
      return;
    }
    sent = true;
    const no = '48.' + String(300 + Math.floor(Math.random()*600));
    $('#reqNo').textContent = no; $('#dlgNo').textContent = 'Nº ' + no;
    $('#dlgGestor').textContent = $('#fGestor').textContent;
    $('#dlgEmail').textContent = $('#fCanal').value==='EMAIL' ? 'Responda o e-mail original com este número para acompanhamento pelo ERP.' : 'Você recebe por e-mail cada mudança de status.';
    const b = $('#statusBadge'); b.textContent = 'Em triagem'; b.className = 'badge sent';
    $('#st1').className='step done'; $('#st2').className='step current';
    $('#wfStatus').textContent = 'Em triagem'; $('#wfNext').textContent = 'Coordenador de Compras — conferir checklist';
    $$('#form input,#form select,#form textarea,#form button').forEach(el => el.disabled = true);
    $('#btnSend').textContent = 'Enviada'; $('#btnSend').disabled = true; $('#btnDraft').disabled = true;
    $('#savedAt').textContent = 'Enviada em ' + new Date().toLocaleString('pt-BR');
    $('#dlg').showModal();
  };
  $('#dlgClose').onclick = () => $('#dlg').close();
  $('#dlgCopy').onclick = () => { navigator.clipboard?.writeText($('#reqNo').textContent).then(()=>toast('Número copiado.'), ()=>toast('Não foi possível copiar.')); };

  // ===== Início =====
  fillCR(); addItem(); update();
})();
</script>
</body>
</html>
