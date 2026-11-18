<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>FlowIT — Painel DRE Executivo e Orçamento Gerencial de TI</title>
<style>
:root{
  --bg:#f6f8fa;--surface:#ffffff;--surface2:#f0f4f6;--line:#dce3e8;
  --text:#17212b;--muted:#5b6875;--brand:#138a66;--brand-soft:#eaf7f2;
  --ok:#138a66;--warn:#b45309;--bad:#b42318;--accent:#006aa6;--accent-soft:#e0f2fe;
  --shadow:0 4px 18px rgb(18 30 42 / 6%);
}
@media (prefers-color-scheme:dark){:root{
  --bg:#0f1511;--surface:#161e18;--surface2:#1b241d;--line:#2a352d;
  --text:#e6ede7;--muted:#9aa89d;--brand:#4cc168;--brand-soft:#1b3322;
  --ok:#4cc168;--warn:#e3a33b;--bad:#ff6b6b;--accent:#c39bf0;--accent-soft:#2a2036;
  --shadow:none;}}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--text);font:14px/1.5 "Segoe UI",system-ui,sans-serif}
.num{font-variant-numeric:tabular-nums;text-align:right;white-space:nowrap}
header{display:flex;align-items:center;gap:16px;padding:18px 24px;background:var(--surface);border-bottom:1px solid var(--line);flex-wrap:wrap}
.logo{width:38px;height:38px;border-radius:10px;background:var(--brand);color:#fff;display:grid;place-items:center;font-weight:700}
h1{font-size:18px;margin:0}
.sub{color:var(--muted);font-size:12px}
.pill{font-size:11px;padding:3px 8px;border-radius:99px;background:var(--brand-soft);color:var(--brand);font-weight:600}
.pill.warn{background:#fff3df;color:var(--warn)}
@media (prefers-color-scheme:dark){.pill.warn{background:#33270f}}
main{padding:20px 24px;max-width:1440px;margin:auto;display:grid;gap:18px}
.card{background:var(--surface);border:1px solid var(--line);border-radius:14px;box-shadow:var(--shadow);padding:16px}
.filters{display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end}
.f{display:flex;flex-direction:column;gap:4px}
.f label{font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em}
input,select,button{font:inherit;color:var(--text);background:var(--surface2);border:1px solid var(--line);border-radius:8px;padding:7px 10px}
input:focus,select:focus,button:focus-visible{outline:2px solid var(--brand);outline-offset:1px}
button{cursor:pointer;font-weight:600}
button.primary{background:var(--brand);color:#fff;border-color:var(--brand)}
.seg{display:inline-flex;border:1px solid var(--line);border-radius:8px;overflow:hidden}
.seg button{border:0;border-radius:0;background:var(--surface2)}
.seg button[aria-pressed=true]{background:var(--brand);color:#fff}
.kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px}
.kpi .l{font-size:12px;color:var(--muted)}
.kpi .v{font-size:24px;font-weight:700;margin-top:2px}
.kpi .d{font-size:12px;margin-top:4px}
.gauge{height:8px;border-radius:99px;background:var(--surface2);margin-top:10px;overflow:hidden;position:relative}
.gauge i{position:absolute;inset:0 auto 0 0;border-radius:99px}
.gauge b{position:absolute;top:-3px;bottom:-3px;width:2px;background:var(--text);left:calc(100%/1.3)}
.grid2{display:grid;grid-template-columns:1.4fr 1fr;gap:18px}
@media (max-width:1000px){.grid2{grid-template-columns:1fr}}
h2{font-size:15px;margin:0 0 12px;display:flex;align-items:center;gap:8px;flex-wrap:wrap}
table{width:100%;border-collapse:collapse}
th{font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted);text-align:left;padding:8px;border-bottom:1px solid var(--line);position:sticky;top:0;background:var(--surface)}
td{padding:8px;border-bottom:1px solid var(--line)}
tr.g1 td{font-weight:700;background:var(--surface2)}
tr.g2 td{font-weight:600}
tr.g2 td:first-child{padding-left:22px}
tr.g3 td:first-child{padding-left:42px;color:var(--muted)}
tr.tot td{font-weight:800;border-top:2px solid var(--text)}
tr.click{cursor:pointer}tr.click:hover td{background:var(--brand-soft)}
.neg{color:var(--bad)}.pos{color:var(--ok)}
.tag{display:inline-block;font-size:11px;padding:1px 7px;border-radius:6px;font-weight:600}
.t-ok{background:var(--brand-soft);color:var(--ok)}
.t-warn{background:#fff3df;color:var(--warn)}
.t-bad{background:#fde7e8;color:var(--bad)}
@media (prefers-color-scheme:dark){.t-warn{background:#33270f}.t-bad{background:#3a1c1e}}
.special{border-color:var(--accent)}
.special h2{color:var(--accent)}
.bars{display:grid;gap:14px}
.bar-row .lbl{display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px}
.stack{display:flex;height:22px;border-radius:6px;overflow:hidden;background:var(--surface2)}
.stack span{display:flex;align-items:center;justify-content:center;font-size:11px;color:#fff;font-weight:600;min-width:0;overflow:hidden}
.legend{display:flex;gap:14px;font-size:12px;color:var(--muted);margin-top:6px}
.legend i{display:inline-block;width:10px;height:10px;border-radius:3px;margin-right:5px;vertical-align:-1px}
.scroll{max-height:420px;overflow:auto}
.empty{padding:24px;text-align:center;color:var(--muted)}
.toolbar{display:flex;gap:8px;margin-left:auto;flex-wrap:wrap}
.match{font-size:11px}
dialog{border:1px solid var(--line);border-radius:14px;background:var(--surface);color:var(--text);max-width:520px;width:92%}
dialog::backdrop{background:rgba(0,0,0,.35)}
.chip{display:inline-flex;gap:6px;align-items:center;background:var(--accent-soft);color:var(--accent);border-radius:99px;padding:3px 10px;font-size:12px;font-weight:600}
footer{color:var(--muted);font-size:11px;text-align:center;padding:10px 0 24px}
</style>
</head>
<body>
<header>
  <img src="/plugins/flowitwhite/public/logo-flowit.png" alt="FlowIT" style="height:36px;object-fit:contain;">
  <div>
    <h1>DRE TI · Real x Orçado</h1>
    <div class="sub">Naturezas 134203 · 132101 · Empresa 1 – Fazenda Progresso · <span id="fonte"></span></div>
  </div>
  <span class="pill" id="pillExcl"></span>
  <div class="toolbar">
    <button id="btnCfg">⚙ Configurar</button>
    <button id="btnImp">⤓ Importar dados (JSON)</button>
    <button class="primary" id="btnCsv">Exportar CSV</button>
  </div>
</header>

<main>
  <section class="card filters" aria-label="Filtros">
    <div class="f"><label for="dIni">Data inicial</label><input type="date" id="dIni" value="2026-09-01"></div>
    <div class="f"><label for="dFim">Data final</label><input type="date" id="dFim" value="2026-09-30"></div>
    <div class="f"><label for="emp">Empresa</label><select id="emp"><option value="1">1 – FAZ PROGRESSO</option></select></div>
    <div class="f"><label>Tipo de movimento</label>
      <div class="seg" role="group" aria-label="Tipo de movimento">
        <button data-tm="O" aria-pressed="true">Pedido de compra</button>
        <button data-tm="C" aria-pressed="false">Nota de compra</button>
      </div></div>
    <div class="f"><label for="cc">Centro de resultado</label><select id="cc"><option value="">Todos</option></select></div>
    <div class="f"><label>Grupo especial</label><span class="chip" id="chipGrupo"></span></div>
  </section>

  <section class="kpis" id="kpis" aria-live="polite"></section>

  <div class="grid2">
    <section class="card">
      <h2>DRE por Grupo › Subgrupo › Natureza <span class="sub">(sem grupo especial · clique na natureza para detalhar)</span></h2>
      <div class="scroll"><table id="dre"></table></div>
    </section>
    <section class="card special">
      <h2>Grupo especial × Demais usuários</h2>
      <div class="bars" id="bars"></div>
      <div class="legend"><span><i style="background:var(--accent)"></i>Grupo especial</span><span><i style="background:var(--brand)"></i>Demais</span></div>
      <table id="cmp" style="margin-top:14px"></table>
      <p class="sub" id="impacto" style="margin:10px 0 0"></p>
    </section>
  </div>

  <section class="card special">
    <h2>Lançamentos do grupo especial <span id="spCount" class="tag t-warn"></span></h2>
    <div class="scroll"><table id="tbSp"></table></div>
  </section>

  <section class="card">
    <h2>Detalhamento – demais usuários <span id="detFiltro" class="tag t-ok"></span>
      <input id="busca" placeholder="Buscar parceiro, usuário, nº…" style="margin-left:auto;min-width:240px" aria-label="Buscar">
    </h2>
    <div class="scroll"><table id="tbDet"></table></div>
  </section>
  <footer>Leitura em modo somente consulta via MCP Sankhya · Valores conferidos na TGFCAB (VLRNOTA). Orçado informado manualmente em ⚙ Configurar.</footer>
</main>

<dialog id="dlgCfg">
  <form method="dialog">
    <h2>Configurações</h2>
    <div class="f"><label for="cfgGrupo">Códigos do grupo especial (separados por vírgula)</label><input id="cfgGrupo"></div>
    <p class="sub">Orçado mensal por natureza (R$)</p>
    <div class="f"><label for="orc132101">132101 – Telefone e Internet</label><input id="orc132101" type="number" step="0.01"></div>
    <div class="f" style="margin-top:8px"><label for="orc134203">134203 – Software / Manut. Sistemas</label><input id="orc134203" type="number" step="0.01"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px"><button value="cancel">Cancelar</button><button class="primary" id="cfgOk" value="ok">Aplicar</button></div>
  </form>
</dialog>
<input type="file" id="file" accept=".json" hidden>

<script>
/* Snapshot extraído do Sankhya (FAZPROGRESSO_TREINA) em 08/10/2026 – TGFCAB, CODEMP=1, TIPMOV O/C
   [NUNOTA, NUMNOTA, DTNEG, CODNAT, TIPMOV, CODTIPOPER, CODCENCUS, CODUSUINC, NOMEUSU, NOMEPARC, VLRNOTA] */
let RAW=[
[1758154,76677,"2026-09-01",134203,"O",4,12020000,348,"ANA SOUSA","ARQUIVAR TECNOLOGIA LTDA",4295.13],
[1758168,76678,"2026-09-01",134203,"O",4,12020000,348,"ANA SOUSA","ESPRESSO CONTROLE DE DESPESAS LTDA",331.41],
[1758197,76679,"2026-09-01",134203,"O",4,12020000,348,"ANA SOUSA","SANKHYA TECNOLOGIA EM SISTEMAS LTDA",42325.98],
[1759275,76732,"2026-09-01",134203,"O",4,12020000,348,"ANA SOUSA","AUTOMATIZACOES SEC XXI TRAT DE DADOS SA",5850],
[1759911,3878,"2026-09-01",134203,"C",130,12020000,235,"HELEN","ARQUIVAR TECNOLOGIA LTDA",4295.13],
[1759915,15302,"2026-09-01",134203,"C",130,12020000,235,"HELEN","ESPRESSO CONTROLE DE DESPESAS LTDA",331.41],
[1759972,42981,"2026-09-01",134203,"C",130,12020000,235,"HELEN","SANKHYA TECNOLOGIA EM SISTEMAS LTDA",42325.98],
[1759997,213935,"2026-09-01",134203,"C",130,12020000,235,"HELEN","AUTOMATIZACOES SEC XXI TRAT DE DADOS SA",5850],
[1760161,11030873,"2026-09-01",132101,"C",170,12020000,235,"HELEN","SKY SERVICOS DE BANDA LARGA LTDA",1786.99],
[1758501,76691,"2026-09-02",134203,"O",4,12020000,348,"ANA SOUSA","ASIS TAXTECH S.A.",3139.24],
[1758503,76692,"2026-09-02",134203,"O",4,12020000,348,"ANA SOUSA","ASIS TAXTECH S.A.",745],
[1758689,76700,"2026-09-02",134203,"O",4,13060000,348,"ANA SOUSA","ASSISTE ASSES EM SISTEMAS ADM S/S LTDA.",11617],
[1758696,76701,"2026-09-02",134203,"O",4,13060000,348,"ANA SOUSA","ASSISTE ASSES EM SISTEMAS ADM S/S LTDA.",5375],
[1759989,9539277,"2026-09-02",132101,"C",170,12020000,235,"HELEN","TIM S/A",3920.92],
[1760067,24239,"2026-09-02",134203,"C",130,12020000,235,"HELEN","ASIS TAXTECH S.A.",745],
[1760085,24261,"2026-09-02",134203,"C",130,12020000,235,"HELEN","ASIS TAXTECH S.A.",3139.24],
[1760091,792,"2026-09-02",134203,"C",130,13060000,235,"HELEN","ASSISTE ASSES EM SISTEMAS ADM S/S LTDA.",10902.56],
[1760110,791,"2026-09-02",134203,"C",130,13060000,235,"HELEN","ASSISTE ASSES EM SISTEMAS ADM S/S LTDA.",5044.44],
[1762307,644653,"2026-09-02",134203,"C",130,12020000,235,"HELEN","CONNECT INFORMACOES E TECNOLOGIA SA",8155.78],
[1763709,76917,"2026-09-02",134203,"O",4,12020000,138,"MAILTON","HERBERT RIBEIRO ROCHA 02104238544",8387],
[1763969,76938,"2026-09-02",134203,"O",4,12020000,1,"EDER","PARIPASSU APLICATIVOS ESPECIALIZADO LTDA",1202.24],
[1764215,20911,"2026-09-02",134203,"C",130,12020000,235,"HELEN","PARIPASSU APLICATIVOS ESPECIALIZADO LTDA",1202.24],
[1765466,59,"2026-09-02",134203,"C",130,12020000,235,"HELEN","HERBERT RIBEIRO ROCHA 02104238544",8387],
[1758720,76704,"2026-09-03",132101,"O",4,12020000,348,"ANA SOUSA","TIM S/A",3920.92],
[1759875,76761,"2026-09-03",132101,"O",4,12020000,348,"ANA SOUSA","TIM S/A",6248.91],
[1759895,1791,"2026-09-03",132101,"C",170,12020000,235,"HELEN","TIM S/A",6248.91],
[1763060,525867,"2026-09-03",134203,"C",130,12020000,235,"HELEN","ASSERTIVA TECNOLOGIA DA INFORM LTDA",1205.1],
[1759931,76762,"2026-09-04",132101,"O",4,12020000,206,"RAFAELA","SKY SERVICOS DE BANDA LARGA LTDA",1786.99],
[1760469,76782,"2026-09-04",134203,"O",4,12020000,206,"RAFAELA","CONNECT INFORMACOES E TECNOLOGIA SA",8155.78],
[1761692,76790,"2026-09-07",134203,"O",4,12020000,348,"ANA SOUSA","D4S SERVICOS EM TECNOLOGIA LTDA",1204.16],
[1762388,732748,"2026-09-07",134203,"C",130,12020000,235,"HELEN","D4S SERVICOS EM TECNOLOGIA LTDA",1204.16],
[1762537,76836,"2026-09-08",132101,"O",4,12020000,348,"ANA SOUSA","ELETROINFO TELECOMUNICAÇÕES LTDA",3000],
[1762603,10999,"2026-09-08",132101,"C",170,12020000,235,"HELEN","ELETROINFO TELECOMUNICAÇÕES LTDA",3000],
[1763052,76865,"2026-09-09",134203,"O",4,12020000,206,"RAFAELA","ASSERTIVA TECNOLOGIA DA INFORM LTDA",1205.1],
[1764395,3029,"2026-09-09",134203,"C",130,12020000,235,"HELEN","DATATEM SOLUCOES LTDA - DEMAIS",1812.5],
[1763711,76920,"2026-09-10",134203,"O",4,12020000,348,"ANA SOUSA","INGRAM MICRO BRASIL LTDA.",391.96],
[1763842,325078,"2026-09-10",134203,"C",130,12020000,235,"HELEN","INGRAM MICRO BRASIL LTDA.",391.96],
[1763843,76932,"2026-09-10",132101,"O",4,12020000,348,"ANA SOUSA","PRO SYSTEM COM E TELECOMUNICACOES LTDA",2500],
[1763853,76933,"2026-09-10",132101,"O",4,12020000,348,"ANA SOUSA","PRO SYSTEM COM E TELECOMUNICACOES LTDA",1000],
[1763954,2404,"2026-09-10",132101,"C",170,12020000,235,"HELEN","PRO SYSTEM COM E TELECOMUNICACOES LTDA",2500],
[1763955,2405,"2026-09-10",132101,"C",170,12020000,235,"HELEN","PRO SYSTEM COM E TELECOMUNICACOES LTDA",1000],
[1763968,76937,"2026-09-10",134203,"O",4,12020000,1,"EDER","AUTOCOM TECNOLOGIA LTDA",363.6],
[1764217,145,"2026-09-10",134203,"C",130,12020000,235,"HELEN","AUTOCOM TECNOLOGIA LTDA",363.6],
[1764213,76958,"2026-09-11",134203,"O",4,12020000,360,"DANIELA.B","DATATEM SOLUCOES LTDA - DEMAIS",1812.5],
[1765529,76992,"2026-09-14",134203,"O",4,12020000,348,"ANA SOUSA","SANKHYA TECNOLOGIA EM SISTEMAS LTDA",250],
[1765590,45189,"2026-09-14",134203,"C",130,12020000,235,"HELEN","SANKHYA TECNOLOGIA EM SISTEMAS LTDA",250],
[1766002,77029,"2026-09-14",134203,"O",4,12020000,348,"ANA SOUSA","THOMSON REUTERS BRASIL C TECNOLOGIA LTDA",2022.88],
[1766282,853984,"2026-09-14",134203,"C",130,12020000,235,"HELEN","THOMSON REUTERS BRASIL C TECNOLOGIA LTDA",2022.88]
];
let FONTE="Snapshot FAZPROGRESSO_TREINA · 08/10/2026";

const HIER={132101:{g:"(-) DESPESAS ADM.",s:"TELEFONE/INTERNET/CORREIO/LUZ",d:"TELEFONE E INTERNET"},
            134203:{g:"(-) DESPESAS ADM.",s:"SERVICOS PROFISSIONAIS/SISTEMAS",d:"SOFTWARE - MANUT SISTEMAS"}};
const st={tm:"O",grupo:[348,1],orc:{132101:18550,134203:126540},nat:null,q:""};
try{const s=JSON.parse(localStorage.getItem("dreTiCfg")||"null");if(s){st.grupo=s.grupo;st.orc=s.orc;st.tm=s.tm||"O";}}catch(e){}
const save=()=>{try{localStorage.setItem("dreTiCfg",JSON.stringify({grupo:st.grupo,orc:st.orc,tm:st.tm}))}catch(e){}};

const $=s=>document.querySelector(s);
const brl=v=>v.toLocaleString("pt-BR",{style:"currency",currency:"BRL",minimumFractionDigits:2});
const brl0=v=>v.toLocaleString("pt-BR",{style:"currency",currency:"BRL",maximumFractionDigits:0});
const pct=v=>isFinite(v)?(v*100).toLocaleString("pt-BR",{maximumFractionDigits:1})+"%":"s/ orçamento";
const dt=s=>s.split("-").reverse().join("/");
const rows=()=>RAW.map(r=>({nunota:r[0],numnota:r[1],dtneg:r[2],codnat:r[3],tipmov:r[4],top:r[5],cc:r[6],usu:r[7],nomeusu:r[8],parc:r[9],vlr:+r[10]}));

function meses(a,b){ // fração de meses no intervalo (para ratear o orçado mensal)
  let d=new Date(a+"T00:00"),e=new Date(b+"T00:00"),t=0;
  while(d<=e){const dim=new Date(d.getFullYear(),d.getMonth()+1,0).getDate();t+=1/dim;d.setDate(d.getDate()+1);}
  return t;
}
function filtrados(){
  const a=$("#dIni").value,b=$("#dFim").value,cc=$("#cc").value;
  return rows().filter(r=>r.dtneg>=a&&r.dtneg<=b&&r.tipmov===st.tm&&(!cc||String(r.cc)===cc));
}
const isSp=r=>st.grupo.includes(r.usu);
const sum=a=>a.reduce((s,r)=>s+r.vlr,0);
function status(real,orc){const p=orc?real/orc:Infinity;return p<=1?["t-ok","Dentro"]:p<=1.1?["t-warn","Atenção"]:["t-bad","Estouro"];}

// vínculo pedido→nota (mesmo parceiro, natureza e valor)
function vinculos(){
  const all=rows(),notas=all.filter(r=>r.tipmov==="C"),usadas=new Set(),m=new Map();
  all.filter(r=>r.tipmov==="O").forEach(p=>{
    const n=notas.find(n=>!usadas.has(n.nunota)&&n.parc===p.parc&&n.codnat===p.codnat&&Math.abs(n.vlr-p.vlr)<.01);
    if(n){usadas.add(n.nunota);m.set(p.nunota,n);m.set(n.nunota,p);}
  });return m;
}

function render(){
  const F=filtrados(),main=F.filter(r=>!isSp(r)),sp=F.filter(isSp);
  const fr=meses($("#dIni").value,$("#dFim").value);
  const orc=n=>(st.orc[n]||0)*fr, orcT=orc(132101)+orc(134203);
  const real=sum(main),realAll=sum(F),realSp=sum(sp),varr=orcT-real;
  $("#fonte").textContent=FONTE;
  $("#pillExcl").textContent="Excluídos da visão principal: usuários "+st.grupo.join(", ");
  $("#chipGrupo").textContent=st.grupo.map(c=>{const r=rows().find(x=>x.usu===c);return c+(r?" – "+r.nomeusu:"")}).join(" · ");
  document.querySelectorAll(".seg button").forEach(b=>b.setAttribute("aria-pressed",b.dataset.tm===st.tm));

  // KPIs
  const [cls,lab]=status(real,orcT),at=orcT?real/orcT:0;
  const gaugeColor=cls==="t-ok"?"var(--ok)":cls==="t-warn"?"var(--warn)":"var(--bad)";
  $("#kpis").innerHTML=`
   <div class="card kpi"><div class="l">Orçado no período</div><div class="v num" style="text-align:left">${brl0(orcT)}</div><div class="d sub">${fr.toLocaleString("pt-BR",{maximumFractionDigits:2})} mês(es) rateado(s)</div></div>
   <div class="card kpi"><div class="l">Realizado (sem grupo especial)</div><div class="v num" style="text-align:left">${brl0(real)}</div>
     <div class="gauge" title="% atingido"><i style="width:${Math.min(at/1.3*100,100)}%;background:${gaugeColor}"></i><b></b></div>
     <div class="d"><span class="tag ${cls}">${lab}</span> ${pct(at)} do orçado</div></div>
   <div class="card kpi"><div class="l">Variação (Orçado − Realizado)</div><div class="v num ${varr<0?"neg":"pos"}" style="text-align:left">${brl0(varr)}</div><div class="d sub">Variação % ${pct(orcT?varr/orcT:NaN)}</div></div>
   <div class="card kpi special"><div class="l">Grupo especial</div><div class="v num" style="text-align:left;color:var(--accent)">${brl0(realSp)}</div><div class="d sub">${pct(realAll?realSp/realAll:0)} do total · ${sp.length} lançamento(s)</div></div>
   <div class="card kpi"><div class="l">Lançamentos (visão principal)</div><div class="v" >${main.length}</div><div class="d sub">${st.tm==="O"?"Pedidos de compra":"Notas de compra"}</div></div>`;

  // DRE
  const nats=[132101,134203];let h=`<thead><tr><th>Grupo / Subgrupo / Natureza</th><th class="num">Orçado</th><th class="num">Realizado</th><th class="num">Variação</th><th class="num">Var. %</th><th>Status</th></tr></thead><tbody>`;
  const line=(c,lbl,o,r,click)=>{const v=o-r,[sc,sl]=status(r,o);return `<tr class="${c}${click?" click":""}" ${click?`data-nat="${click}" tabindex="0"`:""}><td>${lbl}</td><td class="num">${brl(-o)}</td><td class="num neg">${brl(-r)}</td><td class="num ${v<0?"neg":"pos"}">${brl(v)}</td><td class="num">${pct(o?v/o:NaN)}</td><td><span class="tag ${sc}">${sl}</span></td></tr>`};
  const rn=n=>sum(main.filter(r=>r.codnat===n));
  h+=line("g1",HIER[132101].g,orcT,real);
  nats.forEach(n=>{h+=line("g2",HIER[n].s,orc(n),rn(n));h+=line("g3",n+" – "+HIER[n].d,orc(n),rn(n),n);});
  h+=line("tot","RESULTADO",orcT,real)+"</tbody>";
  $("#dre").innerHTML=h;
  document.querySelectorAll("#dre tr.click").forEach(tr=>{const go=()=>{st.nat=st.nat==+tr.dataset.nat?null:+tr.dataset.nat;render();$("#tbDet").scrollIntoView({behavior:"smooth",block:"nearest"});};tr.onclick=go;tr.onkeydown=e=>{if(e.key==="Enter")go()}});

  // Comparativo
  let bars="",cmp=`<thead><tr><th>Natureza</th><th class="num">Grupo esp.</th><th class="num">Demais</th><th class="num">Diferença</th><th class="num">Part. %</th></tr></thead><tbody>`;
  const users={};sp.forEach(r=>{users[r.usu]=users[r.usu]||{n:r.nomeusu,v:0,q:0};users[r.usu].v+=r.vlr;users[r.usu].q++;});
  [...nats,"T"].forEach(n=>{
    const a=n==="T"?realSp:sum(sp.filter(r=>r.codnat===n)),b=n==="T"?real:rn(n),t=a+b,p=t?a/t:0;
    const lbl=n==="T"?"Total":n+" – "+HIER[n].d;
    if(n!=="T")bars+=`<div class="bar-row"><div class="lbl"><span>${lbl}</span><span class="num">${brl0(t)}</span></div>
      <div class="stack" role="img" aria-label="Grupo especial ${pct(p)}, demais ${pct(1-p)}"><span style="width:${p*100}%;background:var(--accent)">${p>.12?pct(p):""}</span><span style="width:${(1-p)*100}%;background:var(--brand)">${1-p>.12?pct(1-p):""}</span></div></div>`;
    cmp+=`<tr class="${n==="T"?"tot":""}"><td>${lbl}</td><td class="num" style="color:var(--accent)">${brl(a)}</td><td class="num">${brl(b)}</td><td class="num">${brl(a-b)}</td><td class="num">${pct(p)}</td></tr>`;
  });
  Object.entries(users).forEach(([c,u])=>cmp+=`<tr class="g3"><td>↳ ${c} – ${u.n}</td><td class="num">${brl(u.v)}</td><td class="num sub">${u.q} lanç.</td><td></td><td class="num">${pct(realAll?u.v/realAll:0)}</td></tr>`);
  $("#bars").innerHTML=bars;$("#cmp").innerHTML=cmp+"</tbody>";
  const varAll=orcT?(orcT-realAll)/orcT:NaN;
  $("#impacto").innerHTML=`<b>Impacto no orçamento:</b> variação % sem o grupo especial <b>${pct(orcT?varr/orcT:NaN)}</b> → com o grupo <b class="${orcT-realAll<0?"neg":""}">${pct(varAll)}</b>.`;

  // Tabelas
  const V=vinculos();
  const head=`<thead><tr><th>Dt. Neg.</th><th>Nro. Único</th><th>Nro. Nota</th><th>TOP</th><th>Natureza</th><th>C. Result.</th><th>Parceiro</th><th>Cód. Usu. Inc.</th><th>Usuário</th><th class="num">Valor</th><th>Vínculo</th></tr></thead>`;
  const tr=r=>{const v=V.get(r.nunota);const vl=v?`<span class="tag t-ok match">${r.tipmov==="O"?"Faturado → NU "+v.nunota:"Pedido NU "+v.nunota}</span>`:(r.tipmov==="O"?`<span class="tag t-warn match">Sem nota</span>`:`<span class="tag t-warn match">Sem pedido</span>`);
    return `<tr><td>${dt(r.dtneg)}</td><td class="num">${r.nunota}</td><td class="num">${r.numnota}</td><td class="num">${r.top}</td><td>${r.codnat}</td><td class="num">${r.cc}</td><td>${r.parc}</td><td class="num">${r.usu}</td><td>${r.nomeusu}</td><td class="num">${brl(r.vlr)}</td><td>${vl}</td></tr>`};
  const tbl=(list,id)=>{ if(!list.length){$(id).innerHTML=`<tr><td class="empty">Nenhum lançamento no período e filtros selecionados.</td></tr>`;return;}
    $(id).innerHTML=head+"<tbody>"+list.map(tr).join("")+`<tr class="tot"><td colspan="9">Total (${list.length})</td><td class="num">${brl(sum(list))}</td><td></td></tr></tbody>`;};
  $("#spCount").textContent=sp.length+" lançamento(s) · "+brl(realSp);
  tbl(sp,"#tbSp");
  const q=st.q.toLowerCase();
  const det=main.filter(r=>(!st.nat||r.codnat===st.nat)&&(!q||[r.parc,r.nomeusu,r.nunota,r.numnota,r.usu].join(" ").toLowerCase().includes(q)));
  $("#detFiltro").textContent=st.nat?"Natureza "+st.nat+" ✕":"Todas as naturezas";
  $("#detFiltro").style.cursor=st.nat?"pointer":"default";
  tbl(det,"#tbDet");
}

// Eventos
["#dIni","#dFim","#cc"].forEach(s=>$(s).addEventListener("change",render));
document.querySelectorAll(".seg button").forEach(b=>b.onclick=()=>{st.tm=b.dataset.tm;save();render();});
$("#busca").oninput=e=>{st.q=e.target.value;render();};
$("#detFiltro").onclick=()=>{if(st.nat){st.nat=null;render();}};
$("#btnCfg").onclick=()=>{$("#cfgGrupo").value=st.grupo.join(", ");$("#orc132101").value=st.orc[132101];$("#orc134203").value=st.orc[134203];$("#dlgCfg").showModal();};
$("#dlgCfg").addEventListener("close",()=>{if($("#dlgCfg").returnValue!=="ok")return;
  st.grupo=$("#cfgGrupo").value.split(",").map(s=>parseInt(s)).filter(n=>!isNaN(n));
  st.orc={132101:+$("#orc132101").value||0,134203:+$("#orc134203").value||0};save();render();});
$("#btnImp").onclick=()=>$("#file").click();
$("#file").onchange=e=>{const f=e.target.files[0];if(!f)return;const rd=new FileReader();rd.onload=()=>{try{
  const j=JSON.parse(rd.result),d=j.dados||j;
  RAW=d.map(o=>[o.NUNOTA,o.NUMNOTA,String(o.DTNEG).slice(0,10),o.CODNAT,o.TIPMOV,o.CODTIPOPER,o.CODCENCUS,o.CODUSUINC,String(o.NOMEUSU||"").trim(),String(o.NOMEPARC||"").trim(),o.VLRNOTA]);
  FONTE="Importado: "+f.name+" · "+new Date().toLocaleString("pt-BR");fillCC();render();}catch(err){alert("JSON inválido: "+err.message);}};rd.readAsText(f);};
$("#btnCsv").onclick=()=>{const F=filtrados();const csv=["NUNOTA;NUMNOTA;DTNEG;CODNAT;TIPMOV;TOP;CODCENCUS;CODUSUINC;NOMEUSU;PARCEIRO;VALOR;GRUPO"]
  .concat(F.map(r=>[r.nunota,r.numnota,dt(r.dtneg),r.codnat,r.tipmov,r.top,r.cc,r.usu,r.nomeusu,r.parc,r.vlr.toFixed(2).replace(".",","),isSp(r)?"ESPECIAL":"DEMAIS"].join(";"))).join("\n");
  const a=document.createElement("a");a.href=URL.createObjectURL(new Blob(["﻿"+csv],{type:"text/csv"}));a.download="dre_ti_lancamentos.csv";a.click();};
function fillCC(){const s=$("#cc"),v=s.value;s.innerHTML='<option value="">Todos</option>'+[...new Set(rows().map(r=>r.cc))].sort().map(c=>`<option>${c}</option>`).join("");s.value=v;}
fillCC();render();
</script>
</body>
</html>
