<?php
// Minimal responsive settings page for oh-my-zsh plugin (Unraid Responsive WebGUI compatible)
// This page uses Unraid's tile structure and avoids DOM/title-bar hacks per the migration guide.
?>
<div class="page" id="ohmyzsh-settings">
  <div class="page-title">
    <h1>oh-my-zsh</h1>
  </div>

  <div class="tile">
    <div class="tile-header">
      <div class="tile-header-left">
        <div class="tile-header-main">
          <span class="tile-title">Zsh Environment</span>
          <div class="tile-subtitle">管理 oh-my-zsh 插件与 root shell 配置</div>
        </div>
      </div>
      <div class="tile-header-right">
        <button id="ohmyzsh-refresh" class="btn btn-primary">刷新状态</button>
        <button id="ohmyzsh-setup" class="btn btn-secondary">执行 setup</button>
      </div>
    </div>

    <div class="tile-content">
      <div id="ohmyzsh-status">
        <div class="table-wrapper">
          <table class="ohmyzsh-table">
            <thead><tr><th>项</th><th>状态</th></tr></thead>
            <tbody>
              <tr><td>oh-my-zsh 目录</td><td id="status-omz">检测中…</td></tr>
              <tr><td>.zshrc</td><td id="status-zshrc">检测中…</td></tr>
              <tr><td>zsh 可用</td><td id="status-zshbin">检测中…</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="ohmyzsh-actions">
        <p>说明：这些动作会调用插件脚本（请在终端或插件日志中查看输出）。</p>
      </div>
    </div>
  </div>

  <link rel="stylesheet" href="/plugins/oh-my-zsh/webGui/oh-my-zsh.css">
  <script>
    function ajaxPost(path, data, cb) {
      var xhr = new XMLHttpRequest();
      xhr.open('POST', path, true);
      xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          cb(xhr.status, xhr.responseText);
        }
      };
      xhr.send(JSON.stringify(data || {}));
    }

    document.getElementById('ohmyzsh-refresh').addEventListener('click', function() {
      // call plugin helper endpoint if implemented; fallback to alert
      ajaxPost('/plugins/oh-my-zsh/status', {}, function(status, text) {
        if (status === 200) {
          try {
            var json = JSON.parse(text);
            document.getElementById('status-omz').textContent = json.omz_dir || '无';
            document.getElementById('status-zshrc').textContent = json.zshrc ? '存在' : '缺失';
            document.getElementById('status-zshbin').textContent = json.zsh ? '可用' : '不可用';
          } catch (e) {
            alert('无法解析状态：' + text);
          }
        } else {
          alert('无法刷新状态（后端未实现）。');
        }
      });
    });

    document.getElementById('ohmyzsh-setup').addEventListener('click', function() {
      if (!confirm('将执行 setup 脚本来在 /root 创建 symlink，并尝试 chsh。确认继续？')) return;
      ajaxPost('/plugins/oh-my-zsh/setup', {}, function(status, text) {
        if (status === 200) {
          alert('setup 已触发（请查看插件日志或系统日志以获得详细输出）。');
        } else {
          alert('无法触发 setup（后端未实现）。');
        }
      });
    });

    // try initial refresh (best-effort)
    if (document.readyState === 'complete') document.getElementById('ohmyzsh-refresh').click();
    else window.addEventListener('load', function() { document.getElementById('ohmyzsh-refresh').click(); });
  </script>
</div>
