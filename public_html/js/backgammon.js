/* NQ Backgammon: rules engine, computer opponent and SVG renderer. Human = white (moves 24 → 1). */
(() => {
    'use strict';
    const host = document.getElementById('board');
    if (!host) return;
    const $ = (id) => document.getElementById(id);
    const statusEl = $('status'), pipEl = $('pips'), saveEl = $('save-status');
    const btnRoll = $('btn-roll'), btnUndo = $('btn-undo'), btnNew = $('btn-new');
    const overlay = $('result');

    const S = { w: 1, b: -1 }, OPP = { w: 'b', b: 'w' }, BAR = { w: 25, b: 0 }, OFF = { w: 0, b: 25 };

    /* ---------------- Rules ---------------- */
    function initial() {
        const pts = Array(26).fill(0);
        pts[24] = 2; pts[13] = 5; pts[8] = 3; pts[6] = 5;
        pts[1] = -2; pts[12] = -5; pts[17] = -3; pts[19] = -5;
        return { pts, bar: { w: 0, b: 0 }, off: { w: 0, b: 0 } };
    }
    const clone = (s) => ({ pts: s.pts.slice(), bar: { ...s.bar }, off: { ...s.off } });
    const dist = (c, p) => (c === 'w' ? p : 25 - p);
    const count = (s, c, p) => Math.max(0, s.pts[p] * S[c]);

    function allHome(s, c) {
        if (s.bar[c]) return false;
        for (let p = 1; p <= 24; p++) if (count(s, c, p) && dist(c, p) > 6) return false;
        return true;
    }

    function singles(s, c, die) {
        const res = [], sg = S[c];
        const open = (to) => s.pts[to] * sg >= -1;
        if (s.bar[c]) {
            const to = c === 'w' ? 25 - die : die;
            if (open(to)) res.push({ from: BAR[c], to, die });
            return res;
        }
        const home = allHome(s, c);
        let farthest = 0;
        if (home) for (let p = 1; p <= 24; p++) if (count(s, c, p)) farthest = Math.max(farthest, dist(c, p));
        for (let p = 1; p <= 24; p++) {
            if (s.pts[p] * sg <= 0) continue;
            const to = c === 'w' ? p - die : p + die;
            if (to >= 1 && to <= 24) {
                if (open(to)) res.push({ from: p, to, die });
            } else if (home) {
                const d = dist(c, p);
                if (d === die || (die > d && d === farthest)) res.push({ from: p, to: OFF[c], die });
            }
        }
        return res;
    }

    function apply(s, c, m) {
        const n = clone(s), sg = S[c];
        let hit = false;
        if (m.from === BAR[c]) n.bar[c]--; else n.pts[m.from] -= sg;
        if (m.to === OFF[c]) n.off[c]++;
        else {
            if (n.pts[m.to] === -sg) { n.pts[m.to] = 0; n.bar[OPP[c]]++; hit = true; }
            n.pts[m.to] += sg;
        }
        return { state: n, hit };
    }

    const key = (s) => s.pts.join(',') + '|' + s.bar.w + s.bar.b + '|' + s.off.w + ',' + s.off.b;

    function sequences(s, c, dice) {
        const out = [], seen = new Set();
        (function rec(st, rem, moves) {
            const k = key(st) + '#' + rem.join('');
            if (seen.has(k)) return;
            seen.add(k);
            let any = false;
            const tried = new Set();
            rem.forEach((d, i) => {
                if (tried.has(d)) return;
                tried.add(d);
                for (const m of singles(st, c, d)) {
                    any = true;
                    const r = rem.slice(); r.splice(i, 1);
                    rec(apply(st, c, m).state, r, moves.concat([m]));
                }
            });
            if (!any) out.push({ moves, state: st });
        })(s, dice, []);
        const max = Math.max(...out.map((o) => o.moves.length));
        let res = out.filter((o) => o.moves.length === max);
        if (max === 1 && dice.length === 2 && dice[0] !== dice[1]) {
            const hi = Math.max(...dice);
            if (res.some((o) => o.moves[0].die === hi)) res = res.filter((o) => o.moves[0].die === hi);
        }
        return res;
    }

    function pip(s, c) {
        let t = s.bar[c] * 25;
        for (let p = 1; p <= 24; p++) t += count(s, c, p) * dist(c, p);
        return t;
    }

    /* ---------------- Computer opponent ---------------- */
    function evaluate(s, c) {
        const o = OPP[c], sg = S[c];
        let v = (pip(s, o) - pip(s, c)) + s.off[c] * 3 + s.bar[o] * 14 - s.bar[c] * 14;
        // Is there still contact?
        let myBack = 0, oppBack = 0;
        for (let p = 1; p <= 24; p++) {
            if (count(s, c, p)) myBack = Math.max(myBack, dist(c, p));
            if (count(s, o, p)) oppBack = Math.max(oppBack, dist(o, p));
        }
        if (s.bar[c]) myBack = 25;
        if (s.bar[o]) oppBack = 25;
        const contact = myBack + oppBack > 25;
        if (!contact) return v * 2 + s.off[c] * 6;
        for (let p = 1; p <= 24; p++) {
            const n = s.pts[p] * sg, d = dist(c, p);
            if (n >= 2) { v += 3; if (d <= 6) v += 4; if (d >= 4 && d <= 9) v += 1; }
            if (n === 1) {
                let shots = 0;
                for (let q = 0; q <= 25; q++) {
                    const oc = q === BAR[o] ? s.bar[o] : (q >= 1 && q <= 24 ? count(s, o, q) : 0);
                    if (!oc) continue;
                    const dd = o === 'w' ? q - p : p - q;
                    if (dd > 0 && dd <= 12) shots += dd <= 6 ? 2 : 1;
                }
                v -= Math.min(shots, 8) * (d <= 6 ? 1.6 : 1.1) + (25 - d) * 0.08 * Math.min(shots, 1) * 10;
            }
        }
        return v;
    }
    function bestSequence(s, c, dice) {
        let best = null, bv = -Infinity;
        for (const q of sequences(s, c, dice)) {
            const v = evaluate(q.state, c) + Math.random() * 0.01;
            if (v > bv) { bv = v; best = q; }
        }
        return best;
    }

    /* ---------------- Rendering ---------------- */
    const W = 1000, H = 720, TOP = 30, BOT = 690, MID = 360, R = 28, COLW = 65;
    function colX(p) {
        if (p <= 6) return 870 - 32.5 - (p - 1) * COLW;
        if (p <= 12) return 420 - 32.5 - (p - 7) * COLW;
        if (p <= 18) return 30 + 32.5 + (p - 13) * COLW;
        return 480 + 32.5 + (p - 19) * COLW;
    }
    function checkerXY(s, p, i) {
        const n = Math.abs(s.pts[p]);
        const sp = n > 5 ? Math.min(56, (MID - TOP - 30 - 2 * R) / (n - 1)) : 56;
        return p >= 13 ? [colX(p), TOP + R + 2 + i * sp] : [colX(p), BOT - R - 2 - i * sp];
    }
    function barXY(c, i) { return c === 'w' ? [450, MID + 50 + i * 46] : [450, MID - 50 - i * 46]; }
    function trayXY(c, i) { return c === 'w' ? [935, BOT - 10 - i * 19] : [935, TOP + 10 + i * 19]; }
    function locXY(s, c, loc, after) {
        // loc: point number, BAR[c] or OFF[c]
        if (loc === OFF[c]) return trayXY(c, Math.max(0, s.off[c] - (after ? 1 : 0)));
        if (loc === BAR[c]) return barXY(c, Math.min(2, Math.max(0, s.bar[c] - 1)));
        const n = Math.abs(s.pts[loc]);
        return checkerXY(s, loc, Math.max(0, n - 1));
    }

    const DEFS = `
    <defs>
      <linearGradient id="g-wood" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#8a5530"/><stop offset=".5" stop-color="#6b3d1f"/><stop offset="1" stop-color="#4a2812"/></linearGradient>
      <linearGradient id="g-bar" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#4a2812"/><stop offset=".5" stop-color="#7a4826"/><stop offset="1" stop-color="#4a2812"/></linearGradient>
      <radialGradient id="g-felt" cx=".5" cy=".5" r=".8"><stop offset="0" stop-color="#2e6b45"/><stop offset="1" stop-color="#14361f"/></radialGradient>
      <filter id="f-grain" x="0" y="0" width="100%" height="100%"><feTurbulence type="fractalNoise" baseFrequency=".9" numOctaves="2" seed="3"/><feColorMatrix values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 .08 0"/><feComposite in2="SourceGraphic" operator="in"/></filter>
      <filter id="f-woodgrain" x="0" y="0" width="100%" height="100%"><feTurbulence type="fractalNoise" baseFrequency=".004 .12" numOctaves="3" seed="8"/><feColorMatrix values="0 0 0 0 .1  0 0 0 0 .05  0 0 0 0 0  0 0 0 .35 0"/><feComposite in2="SourceGraphic" operator="in"/></filter>
      <linearGradient id="g-pa" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#f3e2bd"/><stop offset="1" stop-color="#d9bf8c"/></linearGradient>
      <linearGradient id="g-pb" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#b8432c"/><stop offset="1" stop-color="#7d2617"/></linearGradient>
      <linearGradient id="g-pa2" x1="0" y1="1" x2="0" y2="0"><stop offset="0" stop-color="#f3e2bd"/><stop offset="1" stop-color="#d9bf8c"/></linearGradient>
      <linearGradient id="g-pb2" x1="0" y1="1" x2="0" y2="0"><stop offset="0" stop-color="#b8432c"/><stop offset="1" stop-color="#7d2617"/></linearGradient>
      <radialGradient id="g-w" cx=".35" cy=".3" r=".85"><stop offset="0" stop-color="#ffffff"/><stop offset=".55" stop-color="#f1e8d6"/><stop offset="1" stop-color="#bfae8e"/></radialGradient>
      <radialGradient id="g-b" cx=".35" cy=".3" r=".85"><stop offset="0" stop-color="#5b5b66"/><stop offset=".5" stop-color="#26262c"/><stop offset="1" stop-color="#0b0b0e"/></radialGradient>
      <filter id="f-shadow" x="-30%" y="-30%" width="160%" height="160%"><feDropShadow dx="0" dy="3" stdDeviation="2.5" flood-opacity=".55"/></filter>
      <filter id="f-glow" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="5" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
      <linearGradient id="g-die" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffffff"/><stop offset="1" stop-color="#e6dcc8"/></linearGradient>
      <linearGradient id="g-dieb" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#3a3a42"/><stop offset="1" stop-color="#111114"/></linearGradient>
    </defs>`;

    function checkerSVG(x, y, c, cls = '', label = '') {
        const ring = c === 'w' ? 'rgba(90,70,40,.25)' : 'rgba(255,255,255,.14)';
        return `<g class="ck ${cls}" transform="translate(${x.toFixed(1)} ${y.toFixed(1)})">
            <circle r="${R}" fill="url(#g-${c})" stroke="${c === 'w' ? '#9c8b6c' : '#000'}" stroke-width="1.2" filter="url(#f-shadow)"/>
            <circle r="${R - 8}" fill="none" stroke="${ring}" stroke-width="2"/>
            <circle r="${R - 14}" fill="none" stroke="${ring}" stroke-width="1"/>
            ${label ? `<text y="6" text-anchor="middle" class="ck-label ${c}">${label}</text>` : ''}
        </g>`;
    }
    const PIPS = { 1: [[0, 0]], 2: [[-1, -1], [1, 1]], 3: [[-1, -1], [0, 0], [1, 1]], 4: [[-1, -1], [1, -1], [-1, 1], [1, 1]], 5: [[-1, -1], [1, -1], [0, 0], [-1, 1], [1, 1]], 6: [[-1, -1], [1, -1], [-1, 0], [1, 0], [-1, 1], [1, 1]] };
    function dieSVG(x, y, v, c, used, rot) {
        const pc = c === 'w' ? '#1d1d22' : '#f4eee2';
        return `<g transform="translate(${x} ${y}) rotate(${rot})" opacity="${used ? 0.35 : 1}" filter="url(#f-shadow)">
            <rect x="-27" y="-27" width="54" height="54" rx="10" fill="url(#g-${c === 'w' ? 'die' : 'dieb'})" stroke="${c === 'w' ? '#b9ab8f' : '#000'}"/>
            ${PIPS[v].map(([a, b]) => `<circle cx="${a * 14}" cy="${b * 14}" r="5" fill="${pc}"/>`).join('')}
        </g>`;
    }

    function render(opts = {}) {
        const s = G.s, hide = opts.hide;
        let h = `<svg viewBox="0 0 ${W} ${H}" class="board-svg" role="img" aria-label="Backgammon board">${DEFS}`;
        h += `<rect width="${W}" height="${H}" rx="22" fill="url(#g-wood)"/><rect width="${W}" height="${H}" rx="22" fill="#000" filter="url(#f-woodgrain)"/>`;
        h += `<rect x="6" y="6" width="${W - 12}" height="${H - 12}" rx="18" fill="none" stroke="rgba(255,220,170,.18)" stroke-width="2"/>`;
        for (const [x, w] of [[30, 390], [480, 390]]) {
            h += `<rect x="${x}" y="${TOP}" width="${w}" height="${BOT - TOP}" rx="4" fill="url(#g-felt)"/>`;
            h += `<rect x="${x}" y="${TOP}" width="${w}" height="${BOT - TOP}" rx="4" fill="#fff" filter="url(#f-grain)"/>`;
            h += `<rect x="${x}" y="${TOP}" width="${w}" height="${BOT - TOP}" rx="4" fill="none" stroke="rgba(0,0,0,.45)" stroke-width="3"/>`;
        }
        h += `<rect x="420" y="20" width="60" height="${H - 40}" fill="url(#g-bar)"/>`;
        h += `<rect x="890" y="${TOP}" width="90" height="${MID - TOP - 10}" rx="8" fill="#2b170b" stroke="rgba(0,0,0,.5)"/><rect x="890" y="${MID + 10}" width="90" height="${BOT - MID - 10}" rx="8" fill="#2b170b" stroke="rgba(0,0,0,.5)"/>`;

        const targets = new Set(G.targets || []);
        const sources = new Set(G.sources || []);
        for (let p = 1; p <= 24; p++) {
            const x = colX(p), top = p >= 13, alt = p % 2 === 0;
            const tipY = top ? TOP + 280 : BOT - 280, baseY = top ? TOP : BOT;
            const fill = `url(#g-p${alt ? 'b' : 'a'}${top ? '' : '2'})`;
            h += `<polygon points="${x - 30},${baseY} ${x + 30},${baseY} ${x},${tipY}" fill="${fill}" stroke="rgba(0,0,0,.25)"/>`;
            if (targets.has(p)) h += `<polygon class="target-glow" points="${x - 30},${baseY} ${x + 30},${baseY} ${x},${tipY}" fill="rgba(255,213,74,.45)" stroke="#ffd54a" stroke-width="3" filter="url(#f-glow)"/>`;
            h += `<text x="${x}" y="${top ? TOP - 9 : BOT + 20}" text-anchor="middle" class="pt-num">${p}</text>`;
        }
        // checkers on points
        for (let p = 1; p <= 24; p++) {
            const n = Math.abs(s.pts[p]);
            if (!n) continue;
            const c = s.pts[p] > 0 ? 'w' : 'b';
            for (let i = 0; i < n; i++) {
                if (hide && hide.p === p && i === n - 1) continue;
                const [x, y] = checkerXY(s, p, i);
                let cls = '';
                if (i === n - 1 && c === 'w') { if (G.selected === p) cls = 'selected'; else if (sources.has(p)) cls = 'source'; }
                h += checkerSVG(x, y, c, cls);
            }
        }
        // bar
        for (const c of ['w', 'b']) {
            const n = s.bar[c];
            for (let i = 0; i < Math.min(n, 3); i++) {
                if (hide && hide.p === BAR[c] && hide.c === c && i === Math.min(n, 3) - 1) continue;
                const [x, y] = barXY(c, i);
                let cls = '';
                if (c === 'w' && i === Math.min(n, 3) - 1) { if (G.selected === 25) cls = 'selected'; else if (sources.has(25)) cls = 'source'; }
                h += checkerSVG(x, y, c, cls, i === Math.min(n, 3) - 1 && n > 3 ? n : '');
            }
        }
        // borne off
        for (const c of ['w', 'b']) {
            for (let i = 0; i < s.off[c]; i++) {
                const [x, y] = trayXY(c, i);
                h += `<rect x="${x - 36}" y="${y - 8}" width="72" height="16" rx="4" fill="url(#g-${c})" stroke="${c === 'w' ? '#9c8b6c' : '#000'}"/>`;
            }
        }
        if (targets.has(0)) h += `<rect class="target-glow" x="890" y="${MID + 10}" width="90" height="${BOT - MID - 10}" rx="8" fill="rgba(255,213,74,.3)" stroke="#ffd54a" stroke-width="3" filter="url(#f-glow)"/>`;
        // dice
        if (G.dice.length) {
            const c = G.diceColor, cx = c === 'w' ? 675 : 225;
            const dbl = G.dice[0] === G.dice[1], rem = G.remaining.slice();
            const shown = dbl ? [G.dice[0], G.dice[0]] : G.dice;
            shown.forEach((v, i) => {
                let used;
                if (dbl) used = rem.length <= (1 - i) * 2;
                else { const idx = rem.indexOf(v); used = idx < 0; if (idx >= 0) rem.splice(idx, 1); }
                h += dieSVG(cx + (i ? 36 : -36), MID, v, c, used, G.diceRot[i] || 0);
            });
            if (dbl && G.remaining.length && G.phase === 'move') h += `<text x="${cx}" y="${MID + 52}" text-anchor="middle" class="dice-left">${G.remaining.length} move${G.remaining.length > 1 ? 's' : ''} left</text>`;
        }
        // hit areas
        for (let p = 1; p <= 24; p++) {
            const x = colX(p), top = p >= 13;
            h += `<rect class="hit" data-p="${p}" x="${x - 32.5}" y="${top ? TOP : MID}" width="${COLW}" height="${MID - TOP}" fill="transparent"/>`;
        }
        h += `<rect class="hit" data-p="25" x="420" y="${MID}" width="60" height="${BOT - MID}" fill="transparent"/>`;
        h += `<rect class="hit" data-p="0" x="890" y="${MID + 10}" width="90" height="${BOT - MID - 10}" fill="transparent"/>`;
        h += `<g id="anim"></g></svg>`;
        host.innerHTML = h;
    }

    function animate(fromXY, toXY, c) {
        return new Promise((resolve) => {
            const g = host.querySelector('#anim');
            if (!g || matchMedia('(prefers-reduced-motion: reduce)').matches) return resolve();
            g.innerHTML = checkerSVG(fromXY[0], fromXY[1], c, 'moving');
            const node = g.firstElementChild, t0 = performance.now(), dur = 320;
            (function step(t) {
                const k = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - k, 3);
                const x = fromXY[0] + (toXY[0] - fromXY[0]) * e;
                const y = fromXY[1] + (toXY[1] - fromXY[1]) * e - Math.sin(Math.PI * k) * 40;
                node.setAttribute('transform', `translate(${x} ${y}) scale(${1 + Math.sin(Math.PI * k) * 0.12})`);
                k < 1 ? requestAnimationFrame(step) : resolve();
            })(t0);
        });
    }

    /* ---------------- Game flow ---------------- */
    let G;
    const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
    const roll = () => 1 + Math.floor(Math.random() * 6);
    const setStatus = (t) => { statusEl.innerHTML = t; };
    function updatePanel() {
        pipEl.textContent = `Pips: you ${pip(G.s, 'w')} · computer ${pip(G.s, 'b')}`;
        btnRoll.disabled = !(G.phase === 'roll');
        btnUndo.disabled = !(G.phase === 'move' && G.undo.length);
    }

    function newGame() {
        overlay.hidden = true;
        G = { s: initial(), dice: [], remaining: [], diceColor: 'w', diceRot: [], phase: 'busy', undo: [], selected: null, targets: [], sources: [], moves: 0, started: Date.now(), over: false };
        if (saveEl) saveEl.textContent = '';
        render(); updatePanel();
        openingRoll();
    }

    async function shakeDice(color, final) {
        G.diceColor = color;
        for (let i = 0; i < 7; i++) {
            G.dice = [roll(), roll()]; G.remaining = G.dice.slice();
            G.diceRot = [Math.random() * 40 - 20, Math.random() * 40 - 20];
            render(); await sleep(55);
        }
        G.dice = final; G.diceRot = [-8 + Math.random() * 16, -8 + Math.random() * 16];
        G.remaining = final[0] === final[1] ? [final[0], final[0], final[0], final[0]] : final.slice();
        render();
    }

    async function openingRoll() {
        let a, b;
        do { a = roll(); b = roll(); } while (a === b);
        setStatus(`Opening roll: you <b>${a}</b>, computer <b>${b}</b>.`);
        await shakeDice(a > b ? 'w' : 'b', [a, b]);
        await sleep(900);
        if (a > b) { setStatus(`You go first with ${a}-${b}.`); startHumanMoves(); } else { setStatus(`Computer goes first with ${a}-${b}.`); await sleep(500); computerMoves(); }
    }

    function computeHuman() {
        const seqs = sequences(G.s, 'w', G.remaining);
        G.legal = seqs.map((q) => q.moves[0]).filter(Boolean);
        G.sources = [...new Set(G.legal.map((m) => m.from))];
        G.targets = G.selected !== null ? [...new Set(G.legal.filter((m) => m.from === G.selected).map((m) => m.to))] : [];
    }

    async function startHumanMoves() {
        G.phase = 'move'; G.undo = []; G.selected = null;
        computeHuman();
        if (!G.legal.length) {
            setStatus(`You rolled ${G.dice.join('-')}: no legal moves.`);
            render(); updatePanel(); await sleep(1400);
            return endHumanTurn();
        }
        setStatus(`Your move: ${G.dice.join('-')}. Click a glowing checker.`);
        if (G.sources.length === 1) G.selected = G.sources[0], computeHuman();
        render(); updatePanel();
    }

    async function humanMove(m) {
        G.undo.push({ s: G.s, remaining: G.remaining.slice(), moves: G.moves });
        const from = locXY(G.s, 'w', m.from);
        const { state, hit } = apply(G.s, 'w', m);
        G.s = state; G.moves++;
        G.remaining.splice(G.remaining.indexOf(m.die), 1);
        G.selected = null; G.targets = []; G.sources = []; G.phase = 'busy';
        render({ hide: { p: m.to === 0 ? -1 : m.to, c: 'w' } });
        updatePanel();
        await animate(from, locXY(G.s, 'w', m.to, true), 'w');
        if (G.s.off.w === 15) return finish('w');
        G.phase = 'move';
        computeHuman();
        if (!G.remaining.length || !G.legal.length) {
            render(); updatePanel();
            setStatus(hit ? 'Hit! Turn complete.' : 'Turn complete.');
            G.phase = 'busy'; updatePanel();
            await sleep(650);
            return endHumanTurn();
        }
        if (G.sources.length === 1) { G.selected = G.sources[0]; computeHuman(); }
        setStatus(hit ? 'Hit! Keep going.' : `Moves left: ${G.remaining.join(', ')}`);
        render(); updatePanel();
    }

    function endHumanTurn() { G.phase = 'busy'; G.selected = null; G.targets = []; G.sources = []; updatePanel(); computerTurn(); }

    async function computerTurn() {
        setStatus('Computer is rolling…');
        await sleep(350);
        await shakeDice('b', [roll(), roll()]);
        await sleep(450);
        computerMoves();
    }

    async function computerMoves() {
        const best = bestSequence(G.s, 'b', G.remaining);
        if (!best || !best.moves.length) { setStatus(`Computer rolled ${G.dice.join('-')}: no legal moves.`); await sleep(1300); return humanRollPhase(); }
        setStatus(`Computer plays ${G.dice.join('-')}.`);
        for (const m of best.moves) {
            const from = locXY(G.s, 'b', m.from);
            G.s = apply(G.s, 'b', m).state;
            G.remaining.splice(G.remaining.indexOf(m.die), 1);
            render({ hide: { p: m.to === 25 ? -1 : m.to, c: 'b' } });
            await animate(from, locXY(G.s, 'b', m.to, true), 'b');
            render(); updatePanel();
            if (G.s.off.b === 15) return finish('b');
            await sleep(260);
        }
        await sleep(300);
        humanRollPhase();
    }

    function humanRollPhase() {
        G.phase = 'roll';
        setStatus(G.s.bar.w ? 'You are on the bar. <b>Roll</b> to enter.' : 'Your turn. Press <b>Roll</b> (or Space).');
        updatePanel(); render();
    }

    async function doRoll() {
        if (G.phase !== 'roll') return;
        G.phase = 'busy'; updatePanel();
        await shakeDice('w', [roll(), roll()]);
        startHumanMoves();
    }

    function finish(winner) {
        G.over = true; G.phase = 'over'; G.dice = []; render(); updatePanel();
        const loser = OPP[winner], ls = G.s;
        let type = 'single';
        if (ls.off[loser] === 0) {
            type = 'gammon';
            let inHome = ls.bar[loser] > 0;
            for (let p = 1; p <= 24; p++) if (count(ls, loser, p) && dist(winner, p) <= 6) inHome = true;
            if (inHome) type = 'backgammon';
        }
        const pts = { single: 1, gammon: 2, backgammon: 3 }[type];
        const won = winner === 'w';
        setStatus(won ? `You win${type !== 'single' ? ' a ' + type : ''}! +${pts}` : `Computer wins${type !== 'single' ? ' a ' + type : ''}. −${pts}`);
        overlay.querySelector('.r-title').textContent = won ? 'Victory!' : 'Defeat';
        overlay.querySelector('.r-sub').textContent = `${type === 'single' ? 'Single game' : type[0].toUpperCase() + type.slice(1)} · ${won ? '+' : '−'}${pts} point${pts > 1 ? 's' : ''}`;
        overlay.classList.toggle('won', won);
        overlay.hidden = false;
        if (window.BG.loggedIn) {
            saveEl.textContent = 'Saving…';
            fetch(window.BG.saveUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: JSON.stringify({ result: won ? 'win' : 'loss', win_type: type, moves: G.moves, duration_seconds: Math.round((Date.now() - G.started) / 1000) }),
            }).then((r) => r.json()).then((d) => {
                saveEl.textContent = `Saved. Record: ${d.stats.wins}W–${d.stats.losses}L · ${d.stats.points} pts`;
            }).catch(() => { saveEl.textContent = 'Could not save this result.'; });
        }
    }

    /* ---------------- Input ---------------- */
    host.addEventListener('click', (e) => {
        const t = e.target.closest('[data-p]');
        if (!t || !G || G.phase !== 'move') return;
        const p = +t.dataset.p;
        if (G.selected !== null && G.targets.includes(p)) {
            const opts = G.legal.filter((m) => m.from === G.selected && m.to === p).sort((a, b) => a.die - b.die);
            return humanMove(opts[0]);
        }
        if (G.sources.includes(p)) G.selected = G.selected === p ? null : p;
        else G.selected = null;
        computeHuman(); render();
    });
    btnRoll.addEventListener('click', doRoll);
    btnNew.addEventListener('click', () => { if (!G || G.over || G.moves === 0 || confirm('Abandon the current game?')) newGame(); });
    overlay.querySelector('[data-again]').addEventListener('click', newGame);
    btnUndo.addEventListener('click', () => {
        if (G.phase !== 'move' || !G.undo.length) return;
        const u = G.undo.pop();
        G.s = u.s; G.remaining = u.remaining; G.moves = u.moves; G.selected = null;
        computeHuman(); setStatus(`Your move: ${G.dice.join('-')}.`); render(); updatePanel();
    });
    document.addEventListener('keydown', (e) => {
        if (e.code === 'Space' && G && G.phase === 'roll' && !/INPUT|TEXTAREA|BUTTON/.test(document.activeElement.tagName)) { e.preventDefault(); doRoll(); }
    });

    newGame();
})();
