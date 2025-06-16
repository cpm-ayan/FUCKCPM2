<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CyberCPM - Modern Tool</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #0e1117;
      color: #e0e0e0;
      margin: 0;
      padding: 2rem;
      line-height: 1.6;
    }
    h1 {
      font-size: 2.5rem;
      color: #4fc3f7;
    }
    a {
      color: #81c784;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
    code {
      background: #1c1f26;
      padding: 0.2rem 0.5rem;
      border-radius: 4px;
      color: #e0e0e0;
    }
    pre {
      background-color: #1c1f26;
      padding: 1rem;
      border-radius: 6px;
      overflow-x: auto;
    }
    .highlight {
      color: #ffca28;
    }
    ul {
      list-style: square;
      margin-left: 2rem;
    }
    img {
      max-width: 100%;
      border-radius: 8px;
      margin-top: 1rem;
      border: 1px solid #444;
    }
    .section {
      margin-top: 2rem;
    }
    .install-title {
      font-size: 1.3rem;
      margin-top: 1rem;
      color: #4dd0e1;
    }
  </style>
</head>
<body>

  <h1>🚗 CyberCPM</h1>
  <p><span class="highlight">CyberCPM</span> is a powerful tool made to <strong>modify Car Parking Multiplayer 2 accounts</strong> easily — <em>no root or virtual app required</em>.</p>

  <p>You only need an <strong>Access Key</strong>, which you can get from 
    <a href="https://t.me/CyberCPMbot" target="_blank">@CyberCPMbot</a> for free with <strong>1K Credits</strong> balance.
  </p>

  <img src="./assets/tool.png" alt="CyberCPM Tool Preview" />

  <div class="section">
    <h2>✅ Tested On</h2>
    <ul>
      <li><strong>iPhone</strong> (via <code>ish</code>)</li>
      <li><strong>Android</strong> (via <code>Termux</code>)</li>
      <li><strong>Windows</strong></li>
      <li><strong>Linux</strong></li>
    </ul>
  </div>

  <div class="section">
    <h2>⚙️ Installation</h2>
    <pre><code>git clone https://github.com/CyberCPM/cybercpm.git
cd cybercpm
pip install -r requirements.txt
python main.py</code></pre>
  </div>

  <div class="section">
    <h2 id="install-python">🐍 Install Python</h2>

    <div class="install-title">Termux</div>
    <pre><code>apt update && apt upgrade -y
pkg install python -y
pkg install python-pip</code></pre>

    <div class="install-title">Linux</div>
    <pre><code>sudo apt install python
sudo apt install python-pip</code></pre>

    <div class="install-title">Windows</div>
    <p>Download Python from <a href="https://www.python.org/downloads/" target="_blank">python.org</a></p>
    <pre><code>py -3 -m pip install -r requirements.txt
py -3 main.py</code></pre>
  </div>

</body>
</html>