(function(){
    const $ = (q,ctx=document)=>ctx.querySelector(q);
    const $$ = (q,ctx=document)=>Array.from(ctx.querySelectorAll(q));
  
    const api = (acao, params={})=>{
      const u = new URL(`${window.BASE_URL}/controller/RelatorioControl.php`);
      u.searchParams.set('acao', acao);
      if (params.inicio) u.searchParams.set('inicio', params.inicio);
      if (params.fim)    u.searchParams.set('fim', params.fim);
      return fetch(u).then(r=>r.json());
    };
  
    // Filtros
    const inicioEl = $('#filtro-inicio');
    const fimEl    = $('#filtro-fim');
  
    // KPIs
    const kpiReservas = $('#kpi-reservas');
    const kpiFat      = $('#kpi-faturamento');
    const kpiCli      = $('#kpi-clientes');
    const kpiCar      = $('#kpi-carros');
  
    // Charts
    let chCombo, chDonut, chOcupacao, chArea;
  
    function toBRL(v){ return v.toLocaleString('pt-BR',{style:'currency',currency:'BRL'}); }
  
    // ATUALIZADO: Animação de 3 segundos (180 frames)
    const steps = 180; 
    const animDuration = 3000; 
  
    function countUp(el, end, prefix=''){
      const start = 0;
      let i = 0, val = 0;
      const inc = (end - start)/steps;
      const tick = ()=>{
        i++; val = start + inc*i;
        if (i>=steps) val = end;
        el.textContent = prefix ? (prefix + ' ' + val.toFixed(0)) : val.toFixed(0);
        if (i<steps) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    }
    function countUpMoney(el, end){
      let i=0, val=0, inc=end/steps;
      const tick = ()=>{
        i++; val += inc;
        if (i>=steps) val=end;
        el.textContent = toBRL(Math.max(0,val));
        if (i<steps) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    }
  
    function skeletonOff(){
      $$('.skeleton').forEach(e=>{ e.classList.remove('skeleton'); e.classList.add('fade-in'); });
    }
  
    function applyRangeQuick(days){
      const dFim = new Date();
      const dIni = new Date(); dIni.setDate(dFim.getDate() - (days-1));
      inicioEl.value = dIni.toISOString().slice(0,10);
      fimEl.value    = dFim.toISOString().slice(0,10);
      loadAll();
    }
  
    async function loadAll(){
      const params = {inicio: inicioEl.value, fim: fimEl.value};
  
      // KPIs
      const kpis = await api('kpis', params);
      countUp(kpiReservas, (kpis.total_reservas||0));
      countUpMoney(kpiFat, (kpis.faturamento||0));
      countUp(kpiCli, (kpis.clientes_ativos||0));
      countUp(kpiCar, (kpis.carros_alugados||0));
  
      // Séries Diárias (para o gráfico combo)
      const seriesDiarias = await api('series_diarias', params);
      const dias = Array.from(new Set([
        ...(seriesDiarias.reservas||[]).map(x=>x.dia),
        ...(seriesDiarias.faturamento||[]).map(x=>x.dia)
      ])).sort();
  
      const mapR = Object.fromEntries((seriesDiarias.reservas||[]).map(r=>[r.dia, +r.qtd]));
      const mapF = Object.fromEntries((seriesDiarias.faturamento||[]).map(r=>[r.dia, +r.total]));
  
      const dataR = dias.map(d=>mapR[d]||0);
      const dataF = dias.map(d=>mapF[d]||0);
  
      // Combo (barras + linha)
      const ctxC = $('#chart-reservas-faturamento').getContext('2d');
      if (chCombo) chCombo.destroy();
      chCombo = new Chart(ctxC, {
        type: 'bar',
        data: {
          labels: dias,
          datasets: [
            {label:'Reservas', data: dataR, borderWidth:1, backgroundColor:'#2563eb77'},
            {label:'Faturamento', data: dataF, type:'line', tension:.3, borderColor:'#16a34a', backgroundColor:'#16a34a55', yAxisID:'y1'}
          ]
        },
        options: {
          responsive:true,
          animation: {duration: animDuration}, 
          interaction:{mode:'index', intersect:false},
          scales:{
            y:{ beginAtZero:true, position:'left', title:{display:true,text:'Reservas'} },
            y1:{ beginAtZero:true, position:'right', grid:{drawOnChartArea:false}, title:{display:true,text:'Faturamento (R$)'} }
          },
          plugins:{
            tooltip:{ callbacks:{ label:(ctx)=>ctx.dataset.label==='Faturamento'?`${ctx.dataset.label}: ${toBRL(ctx.parsed.y)}`:`${ctx.dataset.label}: ${ctx.parsed.y}` } }
          }
        }
      });
  
      // Donut categorias
      const cats = await api('distribuicao_categoria', params);
      const labelsCat = cats.map(x=>x.categoria || 'Não classificado');
      const dataCat   = cats.map(x=>+x.qtd);
      const ctxD = $('#chart-categorias').getContext('2d');
      if (chDonut) chDonut.destroy();
      chDonut = new Chart(ctxD, {
        type: 'doughnut',
        data: { labels: labelsCat, datasets:[{ data: dataCat, backgroundColor:['#3b82f6','#f97316','#22c55e','#eab308','#a855f7'] }] },
        options:{ responsive:true, animation:{duration: animDuration}, plugins:{legend:{position:'bottom'}} } 
      });
  
      // Barras Horizontais (Ocupação)
      const oc = await api('ocupacao', params);
      const labelsOc = oc.map(x=>x.nome);
      const dataOc   = oc.map(x=>+x.ocupacao_pct);
      const ctxR = $('#chart-ocupacao').getContext('2d');
      if (chOcupacao) chOcupacao.destroy();
      chOcupacao = new Chart(ctxR, {
        type: 'bar',
        data: { 
          labels: labelsOc, 
          datasets: [{ 
            label:'Ocupação (%)', 
            data: dataOc, 
            backgroundColor:'#2563eb77', 
            borderColor:'#2563eb' 
          }] 
        },
        options: { 
          responsive:true, 
          indexAxis: 'y', 
          animation:{duration: animDuration}, 
          scales:{ 
            x:{ beginAtZero: true, max: 100, title: { display: true, text: 'Ocupação (%)'} },
            y: { ticks: { autoSkip: false } }
          },
          plugins: { legend: { display: false } }
        } 
      });
  
      // Área tendência faturamento (AGRUPADO POR MÊS)
      const seriesMensais = await api('series_mensais_fat', params);
      const labelsMensais = seriesMensais.map(x => x.mes); // Ex: '2025-10'
      const dataFatMensal = seriesMensais.map(x => +x.total);

      const ctxA = $('#chart-area-fat').getContext('2d');
      if (chArea) chArea.destroy();
      chArea = new Chart(ctxA, {
        type: 'line',
        data: { 
          labels: labelsMensais, 
          datasets: [{ 
            label:'Faturamento', 
            data: dataFatMensal, 
            fill:true, 
            tension:.3, 
            borderColor:'#16a34a', 
            backgroundColor:'#16a34a44' 
          }] 
        },
        options: { 
          responsive:true, 
          animation:{duration: animDuration},
          scales: {
            x: { title: { display: true, text: 'Mês' }}
          },
          plugins:{ 
            tooltip:{ 
              callbacks:{ label:(ctx)=>`${ctx.dataset.label}: ${toBRL(ctx.parsed.y)}` } 
            } 
          } 
        }
      });
  
      // Rankings
      const topCli = await api('top_clientes', params);
      const topCar = await api('top_carros', params);
      renderRank('#top-clientes', topCli, (r)=>`${r.nome} <small class="text-muted">(${r.email})</small>`, (r)=>`${r.qtd} reservas · ${toBRL(+r.fat||0)}`);
      renderRank('#top-carros',  topCar, (r)=>`${r.nome}`, (r)=>`${r.qtd} reservas · ${toBRL(+r.fat||0)}`);
  
      skeletonOff();
  
      // Clicar em uma barra filtra para aquele dia
      $('#chart-reservas-faturamento').onclick = (evt)=>{
        const points = chCombo.getElementsAtEventForMode(evt,'nearest',{intersect:true},true);
        if (!points.length) return;
        const idx = points[0].index;
        const dia = dias[idx];
        inicioEl.value = dia;
        fimEl.value    = dia;
        loadAll();
      };
    }
  
    function renderRank(sel, data, leftFn, rightFn){
      const el = $(sel);
      el.innerHTML = '';
      (data||[]).forEach((r,i)=>{
        const div = document.createElement('div');
        div.className = 'rank-item fade-in';
        div.innerHTML = `<div><strong>${i+1}º</strong> ${leftFn(r)}</div><div><strong>${rightFn(r)}</strong></div>`;
        el.appendChild(div);
      });
      if (!(data||[]).length){
        el.innerHTML = `<div class="text-muted">Sem dados no período selecionado.</div>`;
      }
    }
  
    // Botões
    $('#btn-aplicar')?.addEventListener('click', loadAll);
    $('#btn-ultimos7')?.addEventListener('click', ()=>applyRangeQuick(7));
    $('#btn-ultimos30')?.addEventListener('click', ()=>applyRangeQuick(30));
  
    // start
    document.addEventListener('DOMContentLoaded', loadAll);
})();