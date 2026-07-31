<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Conexão Igreja')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --bg: #f0ece3;
            --text: #1a1a2e;
            --card-bg: #ffffff;
            --destaque: #c9a84c;
            --shadow: 0 4px 20px rgba(0,0,0,0.08);
            --border-color: #e8e4db;
            --navbar: #1a1a2e;
            --navbar-text: #f0ece3;
        }
        body.dark {
            --bg: #121212;
            --text: #e8e4db;
            --card-bg: #1e1e2e;
            --shadow: 0 4px 20px rgba(0,0,0,0.4);
            --border-color: #2a2a3e;
            --navbar: #0a0a1a;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg);
            color: var(--text);
            transition: 0.3s;
            min-height: 100vh;
        }
        .navbar {
            background: var(--navbar);
            padding: 12px 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 3px solid var(--destaque);
        }
        .nav-container {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .nav-brand {
            color: var(--destaque);
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-brand i { font-size: 1.4rem; }
        .nav-links { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
        .nav-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 0.9rem;
            padding: 6px 12px;
            border-radius: 8px;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .nav-links a:hover { color: white; background: rgba(255,255,255,0.08); }
        .nav-links a.active { color: var(--destaque); background: rgba(212, 175, 55, 0.15); }
        .nav-user { display: flex; align-items: center; gap: 12px; color: white; font-size: 0.9rem; }
        .nav-user .user-avatar {
            width: 35px; height: 35px; border-radius: 50%;
            background: var(--destaque);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #1a1a2e;
        }
        .nav-user .logout-btn {
            background: rgba(244, 67, 54, 0.2);
            border: 1px solid rgba(244, 67, 54, 0.3);
            color: #f44336;
            padding: 6px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.8rem;
            transition: 0.3s;
        }
        .nav-user .logout-btn:hover { background: rgba(244, 67, 54, 0.3); }
       