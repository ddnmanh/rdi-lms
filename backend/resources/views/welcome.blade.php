<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="LMS Admin — Không gian quản lý khóa học, bài học, học viên và báo cáo trong cùng một hệ thống.">
    <meta name="theme-color" content="#f6f8ff">
    <title>LMS Admin | Quản lý đào tạo đơn giản hơn</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('css/layout.css') }}">

    {{-- Đồng bộ theme với trang quản trị, áp dụng trước khi trình duyệt vẽ giao diện. --}}
    <script>
        (() => {
            let preference = null;
            try { preference = localStorage.getItem('lms-theme'); } catch (_) {}
            const isDark = preference === 'dark' ||
                (!preference && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>

    <style>
        /* iOS 18 inspired, scoped to this landing page to avoid impacting admin layouts. */
        html { scroll-padding-top: 112px; }
        html.dark { color-scheme: dark; }
        body.ios-landing {
            --bg: #f6f8ff;
            --surface: rgba(255, 255, 255, .76);
            --surface-strong: rgba(255, 255, 255, .94);
            --surface-soft: rgba(245, 247, 255, .82);
            --ink: #172139;
            --muted: #68748a;
            --line: rgba(112, 133, 171, .16);
            --line-strong: rgba(106, 126, 168, .23);
            --blue: #007aff;
            --blue-hover: #0066df;
            --blue-wash: rgba(0, 122, 255, .095);
            --shadow: 0 20px 65px rgba(38, 73, 132, .085);
            --glass-shadow: 0 20px 70px rgba(25, 55, 110, .115);
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--ink);
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Segoe UI", sans-serif;
            font-size: 15px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            isolation: isolate;
        }
        html.dark body.ios-landing {
            --bg: #090f1e;
            --surface: rgba(23, 33, 53, .76);
            --surface-strong: rgba(25, 36, 57, .96);
            --surface-soft: rgba(28, 41, 65, .83);
            --ink: #f4f7ff;
            --muted: #a0adc3;
            --line: rgba(194, 209, 240, .12);
            --line-strong: rgba(191, 211, 245, .20);
            --blue: #4d9fff;
            --blue-hover: #77b6ff;
            --blue-wash: rgba(72, 151, 255, .15);
            --shadow: 0 22px 70px rgba(0, 0, 0, .23);
            --glass-shadow: 0 22px 85px rgba(0, 0, 0, .38);
        }
        .ios-landing *, .ios-landing *::before, .ios-landing *::after { box-sizing: border-box; }
        .ios-landing a { text-decoration: none; }
        .ios-landing button { font: inherit; cursor: pointer; }
        .ios-landing svg { display: inline-block; vertical-align: middle; }
        .ios-landing ::selection { background: rgba(0, 122, 255, .24); }
        .ios-landing :is(a, button):focus-visible {
            outline: 3px solid var(--blue);
            outline-offset: 4px;
        }
        .ios-container { width: min(1176px, calc(100% - 56px)); margin-inline: auto; }
        .ios-icon { width: 20px; height: 20px; flex: 0 0 auto; }
        .ios-sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
        .ios-skip { position: fixed; top: -90px; left: 20px; z-index: 200; background: var(--surface-strong); color: var(--ink); border-radius: 14px; padding: 12px 18px; }
        .ios-skip:focus { top: 12px; }

        /* Translucent navigation, similar to iOS floating controls. */
        .ios-header { position: sticky; top: 18px; z-index: 80; padding-top: 18px; margin-top: -18px; }
        .ios-nav {
            display: flex; justify-content: space-between; align-items: center;
            gap: 20px; min-height: 76px; padding: 11px 13px 11px 21px;
            background: var(--surface); border: 1px solid var(--line-strong);
            border-radius: 26px; box-shadow: 0 10px 38px rgba(33, 57, 110, .08);
            -webkit-backdrop-filter: blur(26px) saturate(170%);
            backdrop-filter: blur(26px) saturate(170%);
        }
        .ios-brand { display: inline-flex; align-items: center; gap: 11px; min-width: max-content; color: var(--ink); }
        .ios-brand-mark {
            width: 42px; height: 42px; border-radius: 14px; display: grid; place-items: center;
            background: linear-gradient(145deg, #63b6ff 0%, #007aff 55%, #6054ee 100%);
            color: white; box-shadow: inset 0 1px 1px rgba(255,255,255,.45), 0 7px 17px rgba(0,122,255,.25);
        }
        .ios-brand-mark .ios-icon { width: 24px; height: 24px; }
        .ios-brand-name { font-size: 18px; font-weight: 750; letter-spacing: -.75px; }
        .ios-brand-name span { color: var(--muted); font-weight: 500; }
        .ios-links { display: flex; align-items: center; gap: 7px; margin-left: auto; }
        .ios-links a {
            color: var(--muted); font-size: 13px; font-weight: 610;
            border-radius: 13px; padding: 11px 14px; transition: background .2s, color .2s;
        }
        .ios-links a:hover, .ios-links a.is-current { color: var(--ink); background: var(--surface-soft); }
        .ios-nav-actions { display: flex; align-items: center; gap: 10px; }
        .ios-theme-toggle {
            display: grid; place-items: center; width: 42px; height: 42px; border-radius: 15px;
            border: 1px solid var(--line); color: var(--ink); background: var(--surface-soft);
            transition: background .2s, transform .2s;
        }
        .ios-theme-toggle:hover { background: var(--blue-wash); transform: translateY(-1px); }
        .ios-theme-toggle .icon-sun, html.dark .ios-theme-toggle .icon-moon { display: none; }
        html.dark .ios-theme-toggle .icon-sun { display: inline-block; }
        .ios-btn {
            display: inline-flex; justify-content: center; align-items: center; gap: 10px;
            min-height: 48px; padding: 12px 20px; border-radius: 16px;
            font-size: 14px; font-weight: 700; letter-spacing: -.12px;
            transition: transform .23s, box-shadow .23s, background .23s, border-color .23s;
            white-space: nowrap;
        }
        .ios-btn:hover { transform: translateY(-2px); }
        .ios-btn .ios-icon { width: 17px; height: 17px; }
        .ios-btn-primary {
            color: #fff; background: linear-gradient(135deg, #168bff, #0068f0);
            box-shadow: 0 8px 22px rgba(0, 122, 255, .22);
        }
        .ios-btn-primary:hover { background: linear-gradient(135deg, #329cff, #0070e9); box-shadow: 0 12px 28px rgba(0, 122, 255, .30); }
        .ios-btn-quiet { color: var(--ink); background: var(--surface-strong); border: 1px solid var(--line-strong); }
        .ios-btn-quiet:hover { background: var(--surface-soft); box-shadow: var(--shadow); }
        .ios-nav-login { min-height: 42px; padding: 10px 17px; border-radius: 15px; }

        /* Hero and dashboard illustration. No external imagery is required. */
        .ios-hero { position: relative; padding: 100px 0 84px; overflow: clip; }
        .ios-hero::before, .ios-hero::after { content: ""; position: absolute; border-radius: 50%; pointer-events: none; z-index: -1; }
        .ios-hero::before {
            width: 780px; height: 780px; top: -370px; right: -190px;
            background: radial-gradient(circle, rgba(105, 163, 255, .22), rgba(105, 163, 255, 0) 67%);
        }
        .ios-hero::after {
            width: 570px; height: 570px; bottom: -240px; left: -280px;
            background: radial-gradient(circle, rgba(166, 126, 255, .125), transparent 68%);
        }
        .ios-hero-grid { display: grid; grid-template-columns: minmax(0, .92fr) minmax(0, 1.08fr); align-items: center; gap: clamp(36px, 5vw, 76px); }
        .ios-hero-copy { position: relative; z-index: 2; }
        .ios-eyebrow {
            display: inline-flex; align-items: center; gap: 9px;
            padding: 8px 13px; border: 1px solid rgba(0, 122, 255, .16);
            border-radius: 100px; color: var(--blue); background: var(--blue-wash);
            font-size: 11px; font-weight: 750; letter-spacing: .8px; text-transform: uppercase;
        }
        .ios-eyebrow-dot { width: 8px; height: 8px; flex: 0 0 auto; border-radius: 50%; background: #45c18c; box-shadow: 0 0 0 4px rgba(69,193,140,.12); }
        .ios-hero-title { font-size: clamp(43px, 4.55vw, 69px); line-height: 1.09; font-weight: 780; letter-spacing: -3.6px; margin: 24px 0 23px; max-width: 600px; }
        .ios-gradient-text {
            color: #087bfa;
            background: linear-gradient(98deg, #007aff 1%, #6b63ed 58%, #a157ed 100%);
            -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
        }
        .ios-hero-description { max-width: 485px; color: var(--muted); font-size: 16px; line-height: 1.83; margin: 0; }
        .ios-hero-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; margin-top: 32px; }
        .ios-hero-actions .ios-btn { min-height: 53px; padding: 14px 21px; }
        .ios-trust-note { display: flex; align-items: center; gap: 9px; margin-top: 28px; color: var(--muted); font-size: 12px; }
        .ios-trust-note .ios-icon { width: 17px; height: 17px; color: #23aa7a; }
        .ios-stage { position: relative; min-width: 0; perspective: 1200px; }
        .ios-stage-glow {
            position: absolute; width: 95%; height: 87%; left: 4%; top: 7%; z-index: -1;
            background: linear-gradient(125deg, rgba(80, 153, 255, .31), rgba(170, 104, 241, .20));
            filter: blur(45px); border-radius: 50%; opacity: .85;
        }
        .ios-dashboard {
            background: var(--surface-strong); border: 1px solid rgba(255,255,255,.66);
            border-radius: 29px; overflow: hidden;
            box-shadow: var(--glass-shadow), inset 0 0 0 1px var(--line);
            transform: rotate(-1.4deg); transition: transform .45s ease;
        }
        .ios-dashboard:hover { transform: rotate(0deg) translateY(-5px); }
        html.dark .ios-dashboard { border-color: rgba(255,255,255,.12); }
        .ios-dashboard-toolbar {
            height: 46px; display: flex; align-items: center; justify-content: space-between;
            padding: 0 18px; background: var(--surface-soft); border-bottom: 1px solid var(--line);
        }
        .ios-traffic { display: flex; align-items: center; gap: 6px; }
        .ios-traffic span { width: 9px; height: 9px; border-radius: 50%; }
        .ios-traffic span:nth-child(1) { background: #ff6059; }
        .ios-traffic span:nth-child(2) { background: #ffbd2e; }
        .ios-traffic span:nth-child(3) { background: #28c840; }
        .ios-window-label { font-size: 10px; font-weight: 650; color: var(--muted); }
        .ios-demo-tag { color: var(--muted); border: 1px solid var(--line-strong); font-size: 9px; padding: 4px 8px; border-radius: 8px; }
        .ios-dashboard-frame { display: flex; min-height: 410px; }
        .ios-dashboard-sidebar { flex: 0 0 128px; padding: 22px 10px; background: var(--surface-soft); border-right: 1px solid var(--line); }
        .ios-preview-logo { display: flex; align-items: center; gap: 6px; margin: 0 0 27px 7px; font-size: 11px; font-weight: 800; letter-spacing: -.3px; }
        .ios-preview-logo .ios-icon { width: 18px; height: 18px; color: var(--blue); }
        .ios-preview-nav { display: flex; align-items: center; gap: 7px; padding: 10px 8px; border-radius: 10px; color: var(--muted); font-size: 9px; font-weight: 630; white-space: nowrap; }
        .ios-preview-nav .ios-icon { width: 13px; height: 13px; }
        .ios-preview-nav.active { color: var(--blue); background: var(--blue-wash); }
        .ios-dashboard-content { padding: 24px; flex: 1; min-width: 0; }
        .ios-preview-heading { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 19px; }
        .ios-preview-kicker { color: var(--muted); font-size: 10px; }
        .ios-preview-heading h2 { font-size: 18px; letter-spacing: -.6px; font-weight: 780; margin: 3px 0 0; }
        .ios-preview-avatar { width: 31px; height: 31px; border-radius: 11px; display: grid; place-items: center; color: white; background: linear-gradient(135deg, #f3a45f, #d978b6); font-weight: 750; font-size: 12px; }
        .ios-kpi-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 9px; }
        .ios-kpi { min-width: 0; background: var(--surface-soft); border: 1px solid var(--line); padding: 13px 11px; border-radius: 15px; }
        .ios-kpi .ios-icon { width: 15px; height: 15px; margin-bottom: 13px; }
        .ios-kpi:nth-child(1) .ios-icon { color: #007aff; }
        .ios-kpi:nth-child(2) .ios-icon { color: #9b6af6; }
        .ios-kpi:nth-child(3) .ios-icon { color: #20b992; }
        .ios-kpi small { display: block; color: var(--muted); font-size: 9px; white-space: nowrap; }
        .ios-kpi strong { display: block; font-size: 20px; letter-spacing: -.8px; margin-top: 4px; font-weight: 790; }
        .ios-kpi-details { display: grid; grid-template-columns: 1.15fr .85fr; gap: 10px; margin-top: 11px; }
        .ios-progress-box, .ios-chart-box { background: var(--surface-soft); border: 1px solid var(--line); border-radius: 16px; padding: 15px 13px; min-width: 0; }
        .ios-box-title { display: block; font-size: 10px; font-weight: 750; margin-bottom: 14px; }
        .ios-progress-row { margin-top: 12px; }
        .ios-progress-copy { display: flex; align-items: center; justify-content: space-between; gap: 4px; font-size: 9px; color: var(--muted); margin-bottom: 7px; }
        .ios-progress-track { width: 100%; height: 6px; border-radius: 10px; overflow: hidden; background: rgba(124, 150, 194, .16); }
        .ios-progress-fill { height: 100%; border-radius: inherit; background: linear-gradient(90deg, #44a9ff, #6a6af4); }
        .ios-progress-fill.p76 { width: 76%; }
        .ios-progress-fill.p58 { width: 58%; background: linear-gradient(90deg, #a276ff, #d78af5); }
        .ios-chart-bars { display: flex; align-items: end; gap: 6px; height: 92px; border-bottom: 1px dashed var(--line-strong); padding: 0 0 7px; }
        .ios-chart-bars i { flex: 1; display: block; height: var(--h); border-radius: 6px 6px 3px 3px; background: linear-gradient(180deg, #62b4ff 0%, #7c80f7 100%); opacity: .75; }
        .ios-chart-bars i:nth-child(3n) { opacity: 1; }
        .ios-chart-label { font-size: 9px; color: var(--muted); margin-top: 9px; }
        .ios-float-badge {
            position: absolute; right: -17px; bottom: 37px; display: flex; align-items: center; gap: 10px;
            padding: 12px 16px; background: var(--surface-strong); border: 1px solid var(--line-strong);
            border-radius: 17px; box-shadow: var(--glass-shadow);
            -webkit-backdrop-filter: blur(18px); backdrop-filter: blur(18px);
            transform: rotate(3deg); animation: ios-float 5s ease-in-out infinite;
        }
        .ios-float-badge .ios-check { display: grid; place-items: center; width: 32px; height: 32px; border-radius: 11px; color: #fff; background: #2acb93; }
        .ios-float-badge strong { font-size: 11px; display: block; }
        .ios-float-badge small { color: var(--muted); display: block; font-size: 9px; margin-top: 2px; }

        /* Bento features, generously spaced like native iOS cards. */
        .ios-section { padding: 73px 0 32px; }
        .ios-section-heading { text-align: center; max-width: 670px; margin: 0 auto 39px; }
        .ios-section-eyebrow { display: inline-block; color: var(--blue); font-size: 12px; text-transform: uppercase; font-weight: 800; letter-spacing: 1.6px; margin-bottom: 13px; }
        .ios-section-heading h2 { margin: 0; font-size: clamp(31px, 3.4vw, 45px); line-height: 1.16; letter-spacing: -1.8px; font-weight: 780; }
        .ios-section-heading p { margin: 15px auto 0; color: var(--muted); font-size: 15px; line-height: 1.8; }
        .ios-feature-grid { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 17px; }
        .ios-feature {
            position: relative; display: flex; flex-direction: column; min-width: 0; overflow: hidden;
            border: 1px solid var(--line); background: var(--surface); border-radius: 29px;
            padding: 28px; box-shadow: var(--shadow); isolation: isolate;
            transition: transform .27s ease, box-shadow .27s ease, border-color .27s ease;
            -webkit-backdrop-filter: blur(16px); backdrop-filter: blur(16px);
        }
        .ios-feature:hover { transform: translateY(-5px); border-color: rgba(0,122,255,.27); box-shadow: 0 24px 60px rgba(29,74,144,.13); }
        .ios-feature--large { grid-column: span 6; min-height: 313px; }
        .ios-feature--small { grid-column: span 4; min-height: 264px; }
        .ios-feature--wide { grid-column: span 12; min-height: 176px; flex-direction: row; align-items: center; gap: 25px; }
        .ios-feature-icon { display: grid; place-items: center; width: 52px; height: 52px; border-radius: 17px; margin-bottom: 20px; }
        .ios-feature-icon .ios-icon { width: 25px; height: 25px; }
        .ios-tint-blue { color: #087ef7; background: linear-gradient(150deg, #e6f3ff, #d6e7ff); }
        .ios-tint-purple { color: #8553e9; background: linear-gradient(150deg, #f1e9ff, #e7ddff); }
        .ios-tint-orange { color: #e98639; background: linear-gradient(150deg, #fff1df, #ffe5d4); }
        .ios-tint-green { color: #119d7a; background: linear-gradient(150deg, #defaf0, #c8f1e4); }
        .ios-tint-pink { color: #e05794; background: linear-gradient(150deg, #ffeaf3, #ffdaed); }
        .ios-tint-indigo { color: #5d71ee; background: linear-gradient(150deg, #e9edff, #dce2ff); }
        .ios-feature-kicker { color: var(--muted); font-size: 10px; font-weight: 800; letter-spacing: 1.25px; text-transform: uppercase; }
        .ios-feature h3 { margin: 8px 0 10px; font-weight: 760; font-size: 22px; line-height: 1.25; letter-spacing: -.7px; }
        .ios-feature p { margin: 0; max-width: 420px; color: var(--muted); font-size: 13px; line-height: 1.8; }
        .ios-feature--small h3 { font-size: 19px; }
        .ios-feature-art { margin-top: auto; }
        .ios-course-art { display: flex; align-items: center; gap: 8px; padding-top: 20px; flex-wrap: wrap; }
        .ios-course-chip { display: inline-flex; align-items: center; gap: 7px; padding: 8px 11px; color: var(--ink); background: var(--surface-soft); border: 1px solid var(--line); border-radius: 12px; font-size: 11px; font-weight: 650; }
        .ios-course-chip i { display: block; width: 7px; height: 7px; border-radius: 50%; background: #399cff; }
        .ios-course-chip:nth-child(2) i { background: #a17afb; }
        .ios-course-chip:nth-child(3) i { background: #35c69c; }
        .ios-video-art { position: absolute; right: -11px; bottom: -26px; width: 45%; height: 178px; transform: rotate(-8deg); border-radius: 23px; background: linear-gradient(145deg, #b8b3fc, #7d91f7 55%, #64c0ff); border: 5px solid rgba(255,255,255,.65); box-shadow: 0 12px 35px rgba(90, 92, 185, .18); display: grid; place-items: center; }
        .ios-video-art .ios-play { display: grid; place-items: center; width: 55px; height: 55px; border-radius: 20px; background: rgba(255,255,255,.83); color: #6b70ed; box-shadow: 0 8px 20px rgba(40, 50, 110, .13); }
        .ios-feature--video p { max-width: 51%; }
        .ios-avatar-stack { display: flex; align-items: center; padding-top: 25px; }
        .ios-avatar-stack span { display: grid; place-items: center; width: 37px; height: 37px; border-radius: 13px; border: 3px solid var(--surface-strong); color: #fff; font-weight: 770; font-size: 11px; margin-left: -6px; }
        .ios-avatar-stack span:first-child { margin-left: 0; background: linear-gradient(135deg, #55a0fb, #7672e7); }
        .ios-avatar-stack span:nth-child(2) { background: linear-gradient(135deg, #f5a369, #ed6f92); }
        .ios-avatar-stack span:nth-child(3) { background: linear-gradient(135deg, #2acaab, #61aeea); }
        .ios-avatar-stack span:nth-child(4) { background: var(--surface-soft); color: var(--muted); border-color: var(--surface-strong); }
        .ios-quiz-mini { display: flex; flex-direction: column; gap: 7px; margin-top: auto; padding-top: 22px; }
        .ios-quiz-mini span { width: fit-content; display: inline-flex; align-items: center; gap: 7px; color: var(--muted); font-size: 11px; }
        .ios-quiz-mini .ios-icon { width: 16px; height: 16px; color: #2cbd93; }
        .ios-report-mini { display: flex; align-items: end; gap: 7px; height: 57px; margin-top: auto; padding-top: 15px; }
        .ios-report-mini i { display: block; width: 20px; height: var(--h); border-radius: 6px 6px 3px 3px; background: linear-gradient(180deg, #ff8ec0, #9d8af8); opacity: .88; }
        .ios-feature--wide .ios-feature-icon { margin: 0; flex-shrink: 0; }
        .ios-feature--wide .ios-feature-copy { flex: 1; }
        .ios-feature--wide h3 { margin-top: 6px; }
        .ios-wide-deco { width: 82px; height: 82px; flex-shrink: 0; border-radius: 26px; display: grid; place-items: center; background: var(--blue-wash); color: var(--blue); }
        .ios-wide-deco .ios-icon { width: 36px; height: 36px; }

        /* Workflow and final action panel. */
        .ios-workflow { padding: 70px 0 20px; }
        .ios-workflow-heading { text-align: center; margin: 0 0 23px; font-size: 21px; letter-spacing: -.6px; font-weight: 740; }
        .ios-workflow-list { display: grid; grid-template-columns: 1fr auto 1fr auto 1fr; gap: 15px; align-items: center; }
        .ios-workflow-step { display: flex; align-items: center; gap: 12px; padding: 17px 19px; border: 1px solid var(--line); border-radius: 19px; background: var(--surface); }
        .ios-workflow-number { display: grid; place-items: center; width: 33px; height: 33px; flex: 0 0 auto; border-radius: 11px; background: var(--blue-wash); color: var(--blue); font-weight: 800; font-size: 12px; }
        .ios-workflow-step strong { display: block; font-size: 12px; font-weight: 740; }
        .ios-workflow-step small { display: block; color: var(--muted); font-size: 11px; margin-top: 3px; }
        .ios-workflow-arrow { width: 18px; height: 18px; color: var(--muted); }
        .ios-bottom { padding: 85px 0 75px; }
        .ios-cta {
            display: flex; align-items: center; justify-content: space-between; gap: 38px;
            position: relative; isolation: isolate; overflow: hidden; padding: clamp(32px, 5vw, 62px);
            border-radius: 34px; background: linear-gradient(112deg, #172a61 0%, #173e96 56%, #1769cf 100%);
            color: #fff; box-shadow: 0 26px 50px rgba(20, 72, 163, .18);
        }
        .ios-cta::before { content: ""; position: absolute; z-index: -1; top: -150px; right: 8%; width: 390px; height: 390px; border-radius: 50%; background: radial-gradient(circle, rgba(127,185,255,.48), transparent 70%); }
        .ios-cta::after { content: ""; position: absolute; z-index: -1; bottom: -170px; left: 33%; width: 360px; height: 360px; border-radius: 50%; background: radial-gradient(circle, rgba(125,97,245,.42), transparent 70%); }
        .ios-cta-label { display: inline-block; color: #b7d8ff; text-transform: uppercase; letter-spacing: 1.35px; font-size: 11px; font-weight: 760; margin-bottom: 11px; }
        .ios-cta h2 { margin: 0; font-size: clamp(28px, 3vw, 40px); line-height: 1.16; letter-spacing: -1.6px; font-weight: 770; max-width: 580px; }
        .ios-cta p { margin: 15px 0 0; max-width: 570px; color: #d9e8ff; font-size: 14px; line-height: 1.75; }
        .ios-btn-white { color: #0b60cd; background: #fff; min-height: 53px; box-shadow: 0 12px 25px rgba(6,27,88,.14); }
        .ios-btn-white:hover { background: #e8f2ff; }
        .ios-footer { border-top: 1px solid var(--line); }
        .ios-footer-inner { min-height: 81px; display: flex; align-items: center; justify-content: space-between; gap: 18px; color: var(--muted); font-size: 12px; }
        .ios-footer-brand { display: flex; align-items: center; gap: 9px; color: var(--ink); font-weight: 740; }
        .ios-footer-brand .ios-icon { width: 18px; height: 18px; color: var(--blue); }
        @keyframes ios-float { 0%, 100% { translate: 0 0; } 50% { translate: 0 -8px; } }
        @media (max-width: 1060px) {
            .ios-hero-grid { grid-template-columns: 1fr; gap: 55px; }
            .ios-hero { padding-top: 72px; }
            .ios-hero-copy { max-width: 730px; }
            .ios-hero-title { max-width: 700px; }
            .ios-stage { max-width: 700px; width: 100%; margin-inline: auto; }
            .ios-links { gap: 0; }
            .ios-links a { padding-inline: 10px; }
        }
        @media (max-width: 760px) {
            .ios-container { width: min(100% - 32px, 650px); }
            .ios-header { top: 10px; padding-top: 10px; margin-top: -10px; }
            .ios-nav { min-height: 65px; padding: 9px 10px 9px 13px; border-radius: 21px; gap: 8px; }
            .ios-brand-mark { width: 38px; height: 38px; border-radius: 13px; }
            .ios-brand-name { font-size: 16px; }
            .ios-links { display: none; }
            .ios-nav-actions { margin-left: auto; gap: 7px; }
            .ios-nav-login { padding: 9px 12px; font-size: 12px; min-height: 39px; }
            .ios-theme-toggle { width: 39px; height: 39px; border-radius: 13px; }
            .ios-hero { padding: 69px 0 47px; }
            .ios-hero-title { font-size: clamp(39px, 8.1vw, 60px); letter-spacing: -2.4px; }
            .ios-hero-description { font-size: 15px; }
            .ios-feature--large { grid-column: span 12; }
            .ios-feature--small { grid-column: span 6; }
            .ios-workflow-list { grid-template-columns: 1fr; gap: 10px; }
            .ios-workflow-arrow { rotate: 90deg; margin-inline: auto; }
            .ios-cta { align-items: flex-start; flex-direction: column; }
            .ios-bottom { padding-top: 60px; }
        }
        @media (max-width: 530px) {
            .ios-brand-name span { display: none; }
            .ios-hero-title { font-size: 40px; line-height: 1.1; letter-spacing: -2.1px; }
            .ios-hero-description { line-height: 1.74; }
            .ios-hero-actions { gap: 10px; }
            .ios-hero-actions .ios-btn { width: 100%; }
            .ios-dashboard { transform: none; border-radius: 23px; }
            .ios-dashboard:hover { transform: none; }
            .ios-dashboard-frame { min-height: auto; }
            .ios-dashboard-sidebar { display: none; }
            .ios-dashboard-content { padding: 17px 13px 19px; }
            .ios-kpi { padding: 11px 8px; }
            .ios-kpi strong { font-size: 17px; }
            .ios-kpi small { font-size: 8px; }
            .ios-kpi-details { grid-template-columns: 1.2fr .8fr; }
            .ios-progress-box, .ios-chart-box { padding: 13px 9px; }
            .ios-float-badge { right: -4px; bottom: -20px; padding: 9px 11px; }
            .ios-feature--small { grid-column: span 12; min-height: 226px; }
            .ios-feature--large { min-height: 280px; }
            .ios-feature { padding: 24px; border-radius: 24px; }
            .ios-video-art { opacity: .6; width: 42%; }
            .ios-feature--video p { max-width: 69%; position: relative; z-index: 1; }
            .ios-feature--wide { align-items: flex-start; gap: 14px; }
            .ios-feature--wide .ios-feature-icon { width: 45px; height: 45px; }
            .ios-wide-deco { display: none; }
            .ios-section { padding-top: 76px; }
            .ios-section-heading h2 { font-size: 32px; }
            .ios-section-heading p { font-size: 14px; }
            .ios-workflow { padding-top: 56px; }
            .ios-bottom { padding: 55px 0; }
            .ios-cta { border-radius: 27px; }
            .ios-cta .ios-btn { width: 100%; }
            .ios-footer-inner { align-items: flex-start; flex-direction: column; justify-content: center; padding-block: 20px; gap: 9px; }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto !important; }
            .ios-landing *, .ios-landing *::before, .ios-landing *::after { animation: none !important; transition: none !important; }
            .ios-dashboard, .ios-dashboard:hover, .ios-feature:hover, .ios-btn:hover { transform: none !important; }
        }
    </style>
</head>
<body class="ios-landing">
    {{-- Shared inline icons: local, fast and independent from icon/CDN packages. --}}
    <svg class="ios-sr-only" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
        <symbol id="i-book" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6.5c-3.2-2-6.4-2.2-9-1.6v13.3c3-.6 6.2-.2 9 1.8 2.8-2 6-2.4 9-1.8V4.9c-2.6-.6-5.8-.4-9 1.6Z"/><path d="M12 6.5V20"/></symbol>
        <symbol id="i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></symbol>
        <symbol id="i-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.5 14.3A8.5 8.5 0 0 1 9.7 3.5 8.6 8.6 0 1 0 20.5 14.3Z"/></symbol>
        <symbol id="i-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></symbol>
        <symbol id="i-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="11" rx="3"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4.5 4.5L19 7"/></symbol>
        <symbol id="i-grid" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/></symbol>
        <symbol id="i-play" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="4"/><path d="m10 8 6 4-6 4V8Z"/></symbol>
        <symbol id="i-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2H3Zm13-15a3 3 0 0 1 0 6m2 4a5 5 0 0 1 3 5h-3"/></symbol>
        <symbol id="i-quiz" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="3"/><path d="M9 3.5h6M9 11l2 2 4-4M9 17h6"/></symbol>
        <symbol id="i-chart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V12m6 8V5m5 15v-7m5 7V9"/></symbol>
        <symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 20 5v6c0 5.3-3.5 9-8 11-4.5-2-8-5.7-8-11V5l8-3Z"/><path d="m9 12 2 2 4-4"/></symbol>
    </svg>

    <a class="ios-skip" href="#main-content">Đến nội dung chính</a>

    <header class="ios-header">
        <nav class="ios-nav ios-container" aria-label="Điều hướng chính">
            <a href="{{ url('/') }}" class="ios-brand" aria-label="LMS Admin — Trang chủ">
                <span class="ios-brand-mark"><svg class="ios-icon" aria-hidden="true"><use href="#i-book"/></svg></span>
                <span class="ios-brand-name">LMS <span>Admin</span></span>
            </a>
            <div class="ios-links">
                <a class="is-current" href="#main-content">Tổng quan</a>
                <a href="#features">Tính năng</a>
                <a href="#workflow">Quy trình</a>
            </div>
            <div class="ios-nav-actions">
                <button id="theme-toggle" class="ios-theme-toggle" type="button" aria-label="Bật chế độ tối" aria-pressed="false" title="Đổi giao diện sáng / tối">
                    <svg class="ios-icon icon-moon" aria-hidden="true"><use href="#i-moon"/></svg>
                    <svg class="ios-icon icon-sun" aria-hidden="true"><use href="#i-sun"/></svg>
                </button>
                <a href="{{ route('admin.login') }}" class="ios-btn ios-btn-primary ios-nav-login">Đăng nhập <svg class="ios-icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
            </div>
        </nav>
    </header>

    <main id="main-content">
        <section class="ios-hero" aria-labelledby="hero-title">
            <div class="ios-container ios-hero-grid">
                <div class="ios-hero-copy">
                    <span class="ios-eyebrow"><span class="ios-eyebrow-dot" aria-hidden="true"></span>Nền tảng quản lý học tập</span>
                    <h1 id="hero-title" class="ios-hero-title">Quản lý đào tạo.<br><span class="ios-gradient-text">Theo cách đơn giản hơn.</span></h1>
                    <p class="ios-hero-description">Khóa học, nội dung, học viên và kết quả đào tạo — tất cả được kết nối trong một không gian hiện đại, trực quan và dễ sử dụng.</p>
                    <div class="ios-hero-actions">
                        <a class="ios-btn ios-btn-primary" href="{{ route('admin.login') }}">Vào trang quản trị <svg class="ios-icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
                        <a class="ios-btn ios-btn-quiet" href="#features">Khám phá tính năng</a>
                    </div>
                    <p class="ios-trust-note"><svg class="ios-icon" aria-hidden="true"><use href="#i-shield"/></svg>Không gian dành cho quản trị viên và đội ngũ đào tạo</p>
                </div>

                <div class="ios-stage" aria-label="Giao diện LMS Admin mô phỏng bằng dữ liệu minh họa">
                    <div class="ios-stage-glow" aria-hidden="true"></div>
                    <div class="ios-dashboard">
                        <div class="ios-dashboard-toolbar">
                            <div class="ios-traffic" aria-hidden="true"><span></span><span></span><span></span></div>
                            <span class="ios-window-label">LMS Admin / Dashboard</span>
                            <span class="ios-demo-tag">Minh họa</span>
                        </div>
                        <div class="ios-dashboard-frame">
                            <div class="ios-dashboard-sidebar" aria-hidden="true">
                                <div class="ios-preview-logo"><svg class="ios-icon"><use href="#i-book"/></svg>LMS Admin</div>
                                <div class="ios-preview-nav active"><svg class="ios-icon"><use href="#i-grid"/></svg>Tổng quan</div>
                                <div class="ios-preview-nav"><svg class="ios-icon"><use href="#i-book"/></svg>Khóa học</div>
                                <div class="ios-preview-nav"><svg class="ios-icon"><use href="#i-play"/></svg>Bài học</div>
                                <div class="ios-preview-nav"><svg class="ios-icon"><use href="#i-users"/></svg>Thành viên</div>
                                <div class="ios-preview-nav"><svg class="ios-icon"><use href="#i-chart"/></svg>Báo cáo</div>
                            </div>
                            <div class="ios-dashboard-content">
                                <div class="ios-preview-heading">
                                    <div><span class="ios-preview-kicker">Không gian quản trị</span><h2>Tổng quan ✨</h2></div>
                                    <span class="ios-preview-avatar" aria-hidden="true">AD</span>
                                </div>
                                <div class="ios-kpi-grid">
                                    <div class="ios-kpi"><svg class="ios-icon" aria-hidden="true"><use href="#i-book"/></svg><small>Khóa học</small><strong>24</strong></div>
                                    <div class="ios-kpi"><svg class="ios-icon" aria-hidden="true"><use href="#i-users"/></svg><small>Học viên</small><strong>1.248</strong></div>
                                    <div class="ios-kpi"><svg class="ios-icon" aria-hidden="true"><use href="#i-chart"/></svg><small>Hoàn thành</small><strong>86%</strong></div>
                                </div>
                                <div class="ios-kpi-details">
                                    <div class="ios-progress-box">
                                        <span class="ios-box-title">Tiến độ học tập</span>
                                        <div class="ios-progress-row"><div class="ios-progress-copy"><span>Thiết kế giao diện</span><span>76%</span></div><div class="ios-progress-track"><div class="ios-progress-fill p76"></div></div></div>
                                        <div class="ios-progress-row"><div class="ios-progress-copy"><span>Kiến thức cơ bản</span><span>58%</span></div><div class="ios-progress-track"><div class="ios-progress-fill p58"></div></div></div>
                                    </div>
                                    <div class="ios-chart-box">
                                        <span class="ios-box-title">Hoạt động</span>
                                        <div class="ios-chart-bars" aria-hidden="true"><i style="--h:40%"></i><i style="--h:66%"></i><i style="--h:53%"></i><i style="--h:83%"></i><i style="--h:70%"></i><i style="--h:100%"></i></div>
                                        <div class="ios-chart-label">7 ngày gần đây</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ios-float-badge" aria-hidden="true"><span class="ios-check"><svg class="ios-icon"><use href="#i-check"/></svg></span><span><strong>Quản lý liền mạch</strong><small>Mọi thứ trong một nơi</small></span></div>
                </div>
            </div>
        </section>

        <section class="ios-section ios-container" id="features" aria-labelledby="features-title">
            <div class="ios-section-heading">
                <span class="ios-section-eyebrow">Được thiết kế để tập trung</span>
                <h2 id="features-title">Đơn giản ở bên ngoài.<br>Đầy đủ ở bên trong.</h2>
                <p>Những công cụ cần thiết cho hành trình đào tạo, được sắp xếp khoa học để bạn tập trung vào điều quan trọng.</p>
            </div>
            <div class="ios-feature-grid">
                <article class="ios-feature ios-feature--large">
                    <div class="ios-feature-icon ios-tint-blue"><svg class="ios-icon" aria-hidden="true"><use href="#i-book"/></svg></div>
                    <span class="ios-feature-kicker">Khóa học</span>
                    <h3>Tổ chức khóa học dễ dàng</h3>
                    <p>Tạo, phân loại và quản lý chương trình đào tạo cùng danh sách học viên tham gia.</p>
                    <div class="ios-feature-art ios-course-art" aria-hidden="true"><span class="ios-course-chip"><i></i>Chương trình</span><span class="ios-course-chip"><i></i>Danh mục</span><span class="ios-course-chip"><i></i>Học viên</span></div>
                </article>
                <article class="ios-feature ios-feature--large ios-feature--video">
                    <div class="ios-feature-icon ios-tint-purple"><svg class="ios-icon" aria-hidden="true"><use href="#i-play"/></svg></div>
                    <span class="ios-feature-kicker">Bài học</span>
                    <h3>Nội dung học tập trực quan</h3>
                    <p>Sắp xếp bài học, video và tài liệu theo lộ trình dễ theo dõi.</p>
                    <div class="ios-video-art" aria-hidden="true"><span class="ios-play"><svg class="ios-icon" style="width:27px;height:27px"><use href="#i-play"/></svg></span></div>
                </article>
                <article class="ios-feature ios-feature--small">
                    <div class="ios-feature-icon ios-tint-orange"><svg class="ios-icon" aria-hidden="true"><use href="#i-users"/></svg></div>
                    <span class="ios-feature-kicker">Thành viên</span>
                    <h3>Quản lý người dùng</h3>
                    <p>Quản lý tài khoản, học viên, giảng viên và vai trò trong hệ thống.</p>
                    <div class="ios-feature-art ios-avatar-stack" aria-hidden="true"><span>AN</span><span>HN</span><span>ML</span><span>+</span></div>
                </article>
                <article class="ios-feature ios-feature--small">
                    <div class="ios-feature-icon ios-tint-green"><svg class="ios-icon" aria-hidden="true"><use href="#i-quiz"/></svg></div>
                    <span class="ios-feature-kicker">Bài kiểm tra</span>
                    <h3>Đánh giá kết quả</h3>
                    <p>Thiết kế câu hỏi, bài kiểm tra và đánh giá kiến thức sau bài học.</p>
                    <div class="ios-feature-art ios-quiz-mini" aria-hidden="true"><span><svg class="ios-icon"><use href="#i-check"/></svg>Tạo bộ câu hỏi</span><span><svg class="ios-icon"><use href="#i-check"/></svg>Theo dõi kết quả</span></div>
                </article>
                <article class="ios-feature ios-feature--small">
                    <div class="ios-feature-icon ios-tint-pink"><svg class="ios-icon" aria-hidden="true"><use href="#i-chart"/></svg></div>
                    <span class="ios-feature-kicker">Báo cáo</span>
                    <h3>Nắm bắt tiến độ</h3>
                    <p>Theo dõi khóa học, hoạt động học tập và kết quả đào tạo.</p>
                    <div class="ios-feature-art ios-report-mini" aria-hidden="true"><i style="--h:36%"></i><i style="--h:66%"></i><i style="--h:51%"></i><i style="--h:85%"></i><i style="--h:100%"></i></div>
                </article>
                <article class="ios-feature ios-feature--wide">
                    <div class="ios-feature-icon ios-tint-indigo"><svg class="ios-icon" aria-hidden="true"><use href="#i-shield"/></svg></div>
                    <div class="ios-feature-copy">
                        <span class="ios-feature-kicker">Phân quyền</span>
                        <h3>Đúng người. Đúng quyền truy cập.</h3>
                        <p>Thiết lập vai trò và quyền truy cập phù hợp cho từng nhóm người dùng.</p>
                    </div>
                    <div class="ios-wide-deco" aria-hidden="true"><svg class="ios-icon"><use href="#i-lock"/></svg></div>
                </article>
            </div>
        </section>

        <section class="ios-container ios-workflow" id="workflow" aria-labelledby="workflow-title">
            <h2 id="workflow-title" class="ios-workflow-heading">Một quy trình, mọi thứ kết nối.</h2>
            <div class="ios-workflow-list">
                <div class="ios-workflow-step"><span class="ios-workflow-number">01</span><div><strong>Tạo khóa học</strong><small>Tổ chức nội dung</small></div></div>
                <svg class="ios-icon ios-workflow-arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
                <div class="ios-workflow-step"><span class="ios-workflow-number">02</span><div><strong>Kết nối học viên</strong><small>Phân công và theo dõi</small></div></div>
                <svg class="ios-icon ios-workflow-arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
                <div class="ios-workflow-step"><span class="ios-workflow-number">03</span><div><strong>Đánh giá kết quả</strong><small>Nắm bắt tiến độ</small></div></div>
            </div>
        </section>

        <section class="ios-container ios-bottom" aria-labelledby="access-title">
            <div class="ios-cta">
                <div>
                    <span class="ios-cta-label">LMS Admin</span>
                    <h2 id="access-title">Không gian đào tạo của bạn.<br>Luôn sẵn sàng.</h2>
                    <p>Đăng nhập bằng tài khoản được cấp để bắt đầu quản lý khóa học, thành viên và nội dung học tập.</p>
                </div>
                <a class="ios-btn ios-btn-white" href="{{ route('admin.login') }}">Đăng nhập hệ thống <svg class="ios-icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
            </div>
        </section>
    </main>

    <footer class="ios-footer">
        <div class="ios-container ios-footer-inner">
            <span class="ios-footer-brand"><svg class="ios-icon" aria-hidden="true"><use href="#i-book"/></svg>LMS Admin</span>
            <span>&copy; {{ date('Y') }} LMS. Hệ thống quản lý học tập.</span>
            <span>Thiết kế cho quản trị và đào tạo</span>
        </div>
    </footer>

    <script>
        (() => {
            const toggle = document.getElementById('theme-toggle');
            if (!toggle) return;
            const sync = () => {
                const dark = document.documentElement.classList.contains('dark');
                toggle.setAttribute('aria-pressed', String(dark));
                toggle.setAttribute('aria-label', dark ? 'Bật chế độ sáng' : 'Bật chế độ tối');
                const meta = document.querySelector('meta[name="theme-color"]');
                if (meta) meta.setAttribute('content', dark ? '#090f1e' : '#f6f8ff');
            };
            sync();
            toggle.addEventListener('click', () => {
                const dark = document.documentElement.classList.toggle('dark');
                try { localStorage.setItem('lms-theme', dark ? 'dark' : 'light'); } catch (_) {}
                sync();
            });
        })();
    </script>
</body>
</html>
