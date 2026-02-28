<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    
    <!-- iOS PWA Meta Tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Choo Choo">
    
    <!-- iOS Icon - Must be absolute and non-transparent for best results -->
    <link rel="apple-touch-icon" href="https://www.otbdesign.com/metro/img/icon.png">
    <link rel="apple-touch-icon" sizes="152x152" href="https://www.otbdesign.com/metro/img/icon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.otbdesign.com/metro/img/icon.png">
    <link rel="apple-touch-icon" sizes="167x167" href="https://www.otbdesign.com/metro/img/icon.png">

    <link rel="manifest" href="https://www.otbdesign.com/metro/manifest.json">
    <title>Choo Choo Tracker</title>
    <style>
        :root { 
            --thomas-blue: #00529C;
            --thomas-baby-blue: #77B5FE;
            --thomas-red: #C60C30;
            --thomas-yellow: #FFD700;
            --card-bg: var(--thomas-baby-blue);
            --text-dark: #003366;
            --system-orange: #ff9f0a;
        }
        body { 
            font-family: -apple-system, Helvetica, Arial, sans-serif; 
            background: var(--thomas-blue); color: #fff; margin: 0; padding: 15px; 
            padding-top: env(safe-area-inset-top);
            -webkit-font-smoothing: antialiased;
            display: flex; flex-direction: column; min-height: 100vh;
        }
        .header { border-bottom: 3px solid var(--thomas-red); margin-bottom: 15px; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: baseline; background: var(--thomas-blue); }
        h1 { font-size: 1.2rem; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; margin: 0; color: var(--thomas-yellow); text-shadow: 2px 2px #000; }
        #update-tick { font-size: 0.7rem; color: #fff; font-weight: 600; }

        .route-section { margin-bottom: 12px; border-radius: 12px; overflow: hidden; background: var(--card-bg); flex-shrink: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.4); border: 3px solid var(--thomas-red); }
        .route-btn { 
            width: 100%; padding: 18px; background: var(--card-bg); border: none;
            color: var(--text-dark); text-align: left; font-size: 1.1rem; font-weight: 900;
            display: flex; align-items: center; cursor: pointer; border-bottom: 2px solid rgba(0,0,0,0.1);
        }
        .route-btn i { margin-right: 12px; font-style: normal; font-size: 1.3rem; }
        
        .train-container { display: none; background: #fff; color: var(--text-dark); }
        .train-container.active { display: block; height: auto; }

        .train-row { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid #eee; }
        .train-row:last-child { border-bottom: none; }
        
        .destination { font-weight: 800; color: var(--text-dark); }
        .arrival { font-weight: 900; font-size: 1.1rem; color: #000; }
        .arrival.warning { color: #fff; background: var(--system-orange); padding: 4px 8px; border-radius: 6px; }
        .arrival.danger { color: #fff; background: var(--thomas-red); padding: 4px 8px; border-radius: 6px; animation: pulse 1.5s infinite; }
        .arrival.boarding { color: #fff; background: var(--thomas-blue); padding: 4px 10px; border-radius: 6px; font-size: 0.9rem; animation: pulse-blue 1s infinite; }

        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.6; } 100% { opacity: 1; } }
        @keyframes pulse-blue { 0% { box-shadow: 0 0 0px var(--thomas-blue); } 50% { box-shadow: 0 0 10px var(--thomas-blue); } 100% { box-shadow: 0 0 0px var(--thomas-blue); } }

        .map-wrapper { display: none; margin-bottom: 15px; background: var(--thomas-baby-blue); border-radius: 15px; padding: 20px; height: 200px; position: relative; flex-shrink: 0; border: 3px solid var(--thomas-red); box-shadow: 0 4px 10px rgba(0,0,0,0.4); }
        .map-wrapper.active { display: block; }
        .track-line { position: absolute; left: 30px; top: 30px; bottom: 30px; width: 6px; background: #444; border-radius: 3px; }
        .station-node { position: absolute; left: 26px; width: 14px; height: 14px; background: var(--thomas-yellow); border: 2px solid #000; border-radius: 50%; transform: translateY(-50%); z-index: 2; }
        .station-label { position: absolute; left: 50px; font-size: 0.8rem; color: var(--text-dark); transform: translateY(-50%); font-weight: 800; text-shadow: 1px 1px 0px rgba(255,255,255,0.5); }
        .train-marker { position: absolute; left: 30px; width: 42px; height: 42px; transform: translate(-50%, -50%); z-index: 5; transition: top 2s linear; }
        .train-marker img { width: 100%; height: auto; border-radius: 50%; border: 3px solid var(--thomas-red); background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.5); }

        .alert-box { 
            margin-top: 15px; padding: 15px; border-radius: 12px; border: 4px solid var(--thomas-blue); 
            background: var(--thomas-yellow); display: flex; flex-direction: column; gap: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.4);
            flex: 1; overflow-y: auto;
        }
        .alert-header { font-size: 0.8rem; font-weight: 900; text-transform: uppercase; color: var(--thomas-blue); letter-spacing: 1px; border-bottom: 2px solid var(--thomas-red); padding-bottom: 4px; margin-bottom: 4px; flex-shrink: 0; }
        .alert-content { font-size: 1.1rem; line-height: 1.4; color: var(--thomas-blue); font-weight: 900; }
        .alert-status-good { color: var(--thomas-blue); }
        .alert-status-bad { color: var(--thomas-red); }
    </style>
</head>
<body>

    <div class="header">
        <h1>Choo Choo Tracker</h1>
        <div id="update-tick">LIVE</div>
    </div>

    <div class="route-section">
        <button class="route-btn" onclick="toggleSection('work', 'A04')"><i>🏛️</i> To Work (Judiciary 🟥)</button>
        <div id="work-trains" class="train-container"></div>
    </div>

    <div class="route-section">
        <button class="route-btn" onclick="toggleSection('home', 'B02')"><i>🏠</i> To Home (Woodley Park)</button>
        <div id="home-trains" class="train-container"></div>
    </div>

    <div class="map-wrapper" id="map-area">
        <div class="track-line"></div>
        <div id="stations-layer"></div>
        <div id="trains-layer"></div>
    </div>

    <div class="alert-box">
        <div class="alert-header">Railway Status</div>
        <div id="incidents-content" class="alert-content">Checking status...</div>
    </div>

    <script>
        const MAP_CONFIG = {
            work: {
                stations: [{ name: 'Van Ness', y: 30 }, { name: 'Cleveland Park', y: 95 }, { name: 'WOODLEY PARK', y: 160, bold: true }],
                img: 'https://www.otbdesign.com/metro/img/thomas.png', direction: "1" 
            },
            home: {
                stations: [{ name: 'NoMa', y: 30 }, { name: 'Union Station', y: 95 }, { name: 'JUDICIARY 🟥', y: 160, bold: true }],
                img: 'https://www.otbdesign.com/metro/img/percy.png', direction: "2"
            }
        };

        let currentView = null;

        async function updateIncidents() {
            const el = document.getElementById('incidents-content');
            try {
                const res = await fetch('api.php?type=incidents');
                const data = await res.json();
                const redLineIncidents = data.Incidents.filter(i => i.LinesAffected.includes('RD'));
                if (redLineIncidents.length === 0) {
                    el.innerHTML = '<span class="alert-status-good">● All Engines on Time</span>';
                } else {
                    el.innerHTML = redLineIncidents.map(i => `<span class="alert-status-bad">⚠️ ${i.Description}</span>`).join('<br><br>');
                }
            } catch (e) { el.innerText = 'Status unavailable'; }
        }

        function updateMap(trains) {
            if (!currentView) return;
            const config = MAP_CONFIG[currentView];
            const layer = document.getElementById('trains-layer');
            layer.innerHTML = '';
            trains.forEach(train => {
                const min = (train.Min === 'ARR' || train.Min === 'BRD') ? 0 : parseInt(train.Min);
                if (isNaN(min) || min > 15) return;
                let yPos;
                if (min >= 10) yPos = 30;
                else if (min > 6) { yPos = 95 - (65 * ((min - 6) / 4)); }
                else if (min > 0) { yPos = 160 - (65 * (min / 6)); }
                else { yPos = 160; }
                layer.innerHTML += `<div class="train-marker" style="top: ${yPos}px"><img src="${config.img}"></div>`;
            });
        }

        async function fetchPredictions(stationCode, view) {
            const container = document.getElementById(view + '-trains');
            const config = MAP_CONFIG[view];
            try {
                const res = await fetch(`api.php?type=prediction&station=${stationCode}`);
                const data = await res.json();
                
                const filtered = data.Trains.filter(t => t.Group === config.direction)
                    .sort((a, b) => {
                        const valA = (a.Min === 'ARR' || a.Min === 'BRD') ? 0 : parseInt(a.Min);
                        const valB = (b.Min === 'ARR' || b.Min === 'BRD') ? 0 : parseInt(b.Min);
                        return valA - valB;
                    })
                    .slice(0, 5);

                container.innerHTML = filtered.length ? filtered.map(t => {
                    const isBoard = t.Min === 'BRD';
                    const isArr = t.Min === 'ARR';
                    const min = (isBoard || isArr) ? 0 : parseInt(t.Min);
                    let cls = '';
                    let text = t.Min + (isNaN(t.Min) ? '' : 'm');
                    if (isBoard) { cls = 'boarding'; text = 'ALL BOARD!'; }
                    else if (isArr || min <= 2) { cls = 'danger'; text = isArr ? 'ARRIVING' : t.Min + 'm'; }
                    else if (min <= 4) { cls = 'warning'; }

                    return `<div class="train-row">
                        <span class="destination">${t.DestinationName} <span style="font-size:0.75rem; color:#666; font-weight: 500;">${t.Car || '-'} car</span></span>
                        <span class="arrival ${cls}">${text}</span>
                    </div>`;
                }).join('') : '<div class="train-row">No engines found.</div>';

                updateMap(filtered);
                document.getElementById('update-tick').innerText = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit', second:'2-digit'});
            } catch (e) { container.innerHTML = '<div class="train-row">Offline</div>'; }
        }

        function drawStations(view) {
            currentView = view;
            const config = MAP_CONFIG[view];
            const layer = document.getElementById('stations-layer');
            document.getElementById('map-area').classList.add('active');
            layer.innerHTML = '';
            config.stations.forEach(s => {
                layer.innerHTML += `<div class="station-node" style="top: ${s.y}px"></div>
                <div class="station-label" style="top: ${s.y}px">${s.name}</div>`;
            });
        }

        function toggleSection(view, code) {
            const target = document.getElementById(view + '-trains');
            const otherView = view === 'work' ? 'home' : 'work';
            const otherContainer = document.getElementById(otherView + '-trains');

            if (target.classList.contains('active')) {
                target.classList.remove('active');
                document.getElementById('map-area').classList.remove('active');
                currentView = null;
            } else {
                otherContainer.classList.remove('active');
                target.classList.add('active');
                drawStations(view);
                fetchPredictions(code, view);
            }
        }

        updateIncidents();
        setInterval(updateIncidents, 60000);
        setInterval(() => {
            if (currentView) fetchPredictions(currentView === 'work' ? 'A04' : 'B02', currentView);
        }, 10000);
    </script>
</body>
</html>