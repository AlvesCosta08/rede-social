<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Cartão de Crédito - {{ $membro->nome }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        /* ============================================================
           RESET E CONFIGURAÇÕES GERAIS
           ============================================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            height: 100%;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: clamp(10px, 3vw, 30px);
            margin: 0;
        }

        .container {
            max-width: 520px;
            width: 100%;
            padding: 0 clamp(5px, 2vw, 15px);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ============================================================
           HEADER RESPONSIVO
           ============================================================ */
        .header {
            text-align: center;
            margin-bottom: clamp(15px, 4vw, 30px);
            width: 100%;
        }

        .header h1 {
            color: #d4af37;
            font-size: clamp(1rem, 4vw, 1.6rem);
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: clamp(6px, 1.5vw, 12px);
        }

        .header h1 i {
            font-size: clamp(0.9rem, 3.5vw, 1.5rem);
        }

        .header p {
            color: rgba(255, 255, 255, 0.4);
            font-size: clamp(0.6rem, 1.8vw, 0.9rem);
            margin-top: clamp(2px, 0.5vw, 5px);
        }

        /* ============================================================
           TOAST DE COPIADO
           ============================================================ */
        .toast-copiado {
            position: fixed;
            bottom: clamp(15px, 4vw, 40px);
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.85);
            color: white;
            padding: clamp(10px, 2vw, 16px) clamp(15px, 3vw, 30px);
            border-radius: clamp(10px, 1.5vw, 14px);
            font-weight: 600;
            font-size: clamp(0.75rem, 1.8vw, 0.95rem);
            z-index: 9999;
            backdrop-filter: blur(10px);
            animation: fadeUp 0.4s ease;
            display: none;
            border: 1px solid rgba(212, 175, 55, 0.2);
            max-width: 90vw;
            text-align: center;
        }

        @media (max-width: 400px) {
            .toast-copiado {
                max-width: 85vw;
                font-size: 0.7rem;
                padding: 10px 16px;
            }
        }

        .toast-copiado i {
            color: #4caf50;
            margin-right: 8px;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* ============================================================
           WRAPPER DO CARTÃO - CENTRALIZADO
           ============================================================ */
        .cartao-wrapper {
            perspective: 1500px;
            width: 100%;
            max-width: 440px;
            aspect-ratio: 1.58 / 1;
            margin: 0 auto clamp(15px, 3vw, 28px) auto;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ============================================================
           CARTÃO - EIXO DE ROTAÇÃO CENTRALIZADO
           ============================================================ */
        .cartao {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.8s cubic-bezier(0.4, 0.2, 0.2, 1);
            border-radius: clamp(16px, 3vw, 28px);
            cursor: pointer;
            transform-origin: center center;
        }

        /* Estado virado - rotação no eixo Y central */
        .cartao.virado {
            transform: rotateY(180deg);
        }

        /* ============================================================
           FRENTE E VERSO - POSIÇÃO ABSOLUTA CENTRALIZADA
           ============================================================ */
        .frente,
        .verso {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: clamp(16px, 3vw, 28px);
            overflow: hidden;
            box-shadow: 0 clamp(15px, 3vw, 30px) clamp(30px, 6vw, 60px) rgba(0, 0, 0, 0.6);
        }

        /* ============================================================
           FRENTE DO CARTÃO
           ============================================================ */
        .frente {
            background: linear-gradient(145deg, #0f0c29, #302b63, #24243e);
            color: #f0ece3;
            z-index: 2;
            border: 1px solid rgba(212, 175, 55, 0.25);
            position: relative;
        }

        .frente::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 80%;
            height: 200%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.06) 0%, transparent 70%);
            transform: rotate(25deg);
            pointer-events: none;
        }

        .frente::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -20%;
            width: 60%;
            height: 60%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.04) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .frente .conteudo {
            position: relative;
            z-index: 1;
            height: 100%;
            padding: clamp(14px, 3.5vw, 32px) clamp(14px, 3.5vw, 28px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header do cartão */
        .frente .header-cartao {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .frente .bandeira {
            display: flex;
            align-items: center;
            gap: clamp(4px, 1vw, 10px);
            font-size: clamp(0.55rem, 1.5vw, 0.85rem);
            letter-spacing: 2px;
            font-weight: 700;
            color: #d4af37;
        }

        .frente .bandeira i {
            font-size: clamp(1rem, 3vw, 1.8rem);
            color: #d4af37;
        }

        .frente .bandeira .logo-texto {
            font-size: clamp(0.45rem, 1.2vw, 0.7rem);
            letter-spacing: 2px;
            opacity: 0.7;
            line-height: 1.2;
        }

        .frente .bandeira .logo-texto span {
            display: block;
            font-size: clamp(0.35rem, 0.9vw, 0.55rem);
            opacity: 0.4;
            letter-spacing: 3px;
        }

        .frente .chip {
            background: linear-gradient(145deg, #d4af37, #f2d680, #c9a84c);
            width: clamp(30px, 6vw, 50px);
            height: clamp(22px, 4.5vw, 38px);
            border-radius: clamp(6px, 1.2vw, 12px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(0.8rem, 2vw, 1.4rem);
            color: #8b7536;
            box-shadow: inset 0 2px 6px rgba(255, 255, 255, 0.4), 0 2px 8px rgba(0, 0, 0, 0.3);
            flex-shrink: 0;
        }

        .frente .chip i {
            transform: rotate(90deg);
        }

        /* Badge de status */
        .frente .badge-valid {
            position: absolute;
            top: clamp(10px, 2vw, 20px);
            right: clamp(10px, 2vw, 20px);
            background: rgba(76, 175, 80, 0.15);
            border: 1px solid rgba(76, 175, 80, 0.3);
            padding: clamp(2px, 0.5vw, 5px) clamp(8px, 1.5vw, 16px);
            border-radius: 20px;
            font-size: clamp(0.4rem, 1vw, 0.6rem);
            letter-spacing: 1px;
            color: #4CAF50;
            font-weight: 600;
            z-index: 2;
            backdrop-filter: blur(4px);
            white-space: nowrap;
        }

        .frente .badge-valid.inativo {
            background: rgba(244, 67, 54, 0.15);
            border-color: rgba(244, 67, 54, 0.3);
            color: #f44336;
        }

        .frente .badge-valid i {
            font-size: clamp(0.3rem, 0.7vw, 0.45rem);
            margin-right: 3px;
        }

        /* Body do cartão */
        .frente .body-cartao {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: clamp(6px, 1.5vw, 16px);
            padding: clamp(4px, 1vw, 10px) 0;
        }

        .frente .numero-cartao {
            display: flex;
            gap: clamp(4px, 1vw, 14px);
            font-size: clamp(0.7rem, 2vw, 1.2rem);
            letter-spacing: clamp(2px, 0.6vw, 5px);
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
            flex-wrap: wrap;
        }

        .frente .numero-cartao span {
            background: rgba(255, 255, 255, 0.06);
            padding: clamp(2px, 0.5vw, 6px) clamp(4px, 1vw, 12px);
            border-radius: clamp(4px, 0.8vw, 8px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .frente .info-cartao {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: clamp(4px, 1vw, 12px);
        }

        .frente .info-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
            min-width: 60px;
        }

        .frente .info-item label {
            font-size: clamp(0.4rem, 1vw, 0.6rem);
            letter-spacing: 1px;
            opacity: 0.5;
            text-transform: uppercase;
            font-weight: 600;
        }

        .frente .info-item p {
            font-size: clamp(0.65rem, 1.6vw, 0.95rem);
            font-weight: 600;
            letter-spacing: 0.5px;
            word-break: break-word;
            color: #ffffff;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        }

        .frente .info-item.validade {
            text-align: right;
            flex: 0 0 auto;
            min-width: 50px;
        }

        .frente .info-item.validade p {
            font-size: clamp(0.6rem, 1.4vw, 0.85rem);
            font-weight: 700;
            color: #d4af37;
        }

        /* Footer do cartão */
        .frente .footer-cartao {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: clamp(6px, 1.2vw, 14px);
            border-top: 1px solid rgba(212, 175, 55, 0.12);
            flex-wrap: wrap;
            gap: clamp(3px, 0.8vw, 8px);
        }

        .frente .status-membro {
            display: flex;
            align-items: center;
            gap: clamp(4px, 0.8vw, 10px);
            font-size: clamp(0.5rem, 1.2vw, 0.7rem);
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .frente .status-membro i {
            font-size: clamp(0.35rem, 0.8vw, 0.55rem);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.4;
                transform: scale(0.8);
            }
        }

        .frente .cargo-membro {
            font-size: clamp(0.45rem, 1.1vw, 0.7rem);
            opacity: 0.6;
            display: flex;
            align-items: center;
            gap: clamp(3px, 0.6vw, 8px);
            letter-spacing: 1px;
            font-weight: 600;
        }

        .frente .cargo-membro i {
            color: #d4af37;
            font-size: clamp(0.4rem, 1vw, 0.7rem);
        }

        /* ============================================================
           VERSO DO CARTÃO
           ============================================================ */
        .verso {
            background: #1a1a2a;
            transform: rotateY(180deg);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .verso .verso-conteudo {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .verso .tarja-magnetica {
            background: linear-gradient(180deg, #0a0a0a, #1a1a1a);
            height: clamp(30px, 6vw, 50px);
            margin-top: clamp(15px, 3vw, 30px);
            margin-bottom: clamp(10px, 2vw, 20px);
            width: 100%;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.8), 0 4px 20px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .verso .verso-info {
            padding: 0 clamp(12px, 2.5vw, 25px) clamp(10px, 2vw, 22px);
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .verso .assinatura-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: clamp(6px, 1.5vw, 16px);
        }

        .verso .linha-assinatura {
            flex: 1;
            height: clamp(24px, 4.5vw, 38px);
            background: linear-gradient(180deg, #d5cdc5 0%, #e8e0d8 30%, #f0ece3 50%, #e8e0d8 70%, #d5cdc5 100%);
            border-radius: 6px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .verso .linha-assinatura::after {
            content: 'Assinatura do titular';
            position: absolute;
            bottom: clamp(-14px, -2.5vw, -18px);
            left: 8px;
            font-size: clamp(0.35rem, 0.8vw, 0.5rem);
            color: rgba(255, 255, 255, 0.2);
            letter-spacing: 1px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .verso .codigo-seguranca {
            display: flex;
            align-items: center;
            gap: clamp(4px, 0.8vw, 8px);
            color: #aaa;
            font-size: clamp(0.45rem, 1vw, 0.65rem);
            font-weight: 600;
            letter-spacing: 1px;
            flex-shrink: 0;
        }

        .verso .codigo-seguranca span {
            background: #0a0a0a;
            padding: clamp(2px, 0.5vw, 5px) clamp(8px, 1.5vw, 16px);
            border-radius: 6px;
            color: #e0e0e0;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            font-size: clamp(0.6rem, 1.4vw, 0.9rem);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .verso .verso-detalhes {
            display: flex;
            align-items: center;
            gap: clamp(10px, 2.5vw, 22px);
            margin-top: clamp(10px, 2vw, 22px);
            padding-top: clamp(8px, 1.5vw, 16px);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .verso .verso-detalhes .qr-code {
            width: clamp(65px, 12vw, 100px);
            height: clamp(65px, 12vw, 100px);
            background: white;
            padding: clamp(4px, 0.8vw, 8px);
            border-radius: clamp(8px, 1.5vw, 14px);
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .verso .verso-detalhes .qr-code img,
        .verso .verso-detalhes .qr-code canvas {
            width: 100% !important;
            height: 100% !important;
            border-radius: 6px;
        }

        .verso .verso-texto {
            flex: 1;
            font-size: clamp(0.45rem, 1vw, 0.65rem);
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.6;
            min-width: 0;
        }

        .verso .verso-texto .igreja-nome {
            color: #d4af37;
            font-weight: 700;
            font-size: clamp(0.55rem, 1.3vw, 0.8rem);
            letter-spacing: 2px;
            display: block;
            margin-bottom: 2px;
        }

        .verso .verso-texto .igreja-endereco,
        .verso .verso-texto .igreja-contato {
            font-size: clamp(0.4rem, 0.9vw, 0.6rem);
            opacity: 0.5;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        @media (max-width: 400px) {
            .verso .verso-texto .igreja-endereco,
            .verso .verso-texto .igreja-contato {
                font-size: 0.4rem;
            }
        }

        .verso .verso-texto i {
            margin-right: 3px;
            color: #d4af37;
            width: clamp(10px, 2vw, 14px);
            text-align: center;
            font-size: clamp(0.4rem, 0.9vw, 0.6rem);
            flex-shrink: 0;
        }

        .verso .verso-texto .info-extra {
            margin-top: clamp(3px, 0.6vw, 8px);
            padding-top: clamp(3px, 0.6vw, 8px);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: clamp(0.35rem, 0.8vw, 0.55rem);
            opacity: 0.3;
            letter-spacing: 0.5px;
            word-break: break-word;
        }

        /* ============================================================
           BOTÕES DE AÇÃO
           ============================================================ */
        .botoes {
            display: flex;
            gap: clamp(6px, 1.5vw, 14px);
            flex-wrap: wrap;
            justify-content: center;
            width: 100%;
            max-width: 440px;
        }

        .botoes .btn {
            padding: clamp(8px, 1.8vw, 14px) clamp(12px, 2.5vw, 24px);
            border: none;
            border-radius: clamp(8px, 1.5vw, 14px);
            font-weight: 700;
            cursor: pointer;
            font-size: clamp(0.65rem, 1.5vw, 0.9rem);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: clamp(4px, 0.8vw, 10px);
            text-decoration: none;
            flex: 1;
            justify-content: center;
            min-width: clamp(60px, 12vw, 100px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }

        .botoes .btn:active {
            transform: scale(0.94);
        }

        .botoes .btn i {
            font-size: clamp(0.6rem, 1.3vw, 0.9rem);
        }

        .btn-girar {
            background: linear-gradient(145deg, #2c3e50, #34495e);
            color: white;
        }

        .btn-girar:hover {
            background: linear-gradient(145deg, #34495e, #3d566e);
        }

        .btn-whatsapp {
            background: linear-gradient(145deg, #25d366, #128C7E);
            color: white;
        }

        .btn-whatsapp:hover {
            background: linear-gradient(145deg, #2de072, #15a085);
        }

        .btn-imprimir {
            background: linear-gradient(145deg, #3498db, #2980b9);
            color: white;
        }

        .btn-imprimir:hover {
            background: linear-gradient(145deg, #3da8e3, #2e8fc4);
        }

        .btn-voltar {
            background: linear-gradient(145deg, #e74c3c, #c0392b);
            color: white;
        }

        .btn-voltar:hover {
            background: linear-gradient(145deg, #f05a4a, #d44637);
        }

        .btn-copiar {
            background: linear-gradient(145deg, #8e44ad, #6c3483);
            color: white;
        }

        .btn-copiar:hover {
            background: linear-gradient(145deg, #9b59b6, #7d3c98);
        }

        /* ============================================================
           RESPONSIVO
           ============================================================ */

        /* Telas muito pequenas (até 320px) */
        @media (max-width: 320px) {
            .container {
                padding: 0 4px;
            }

            .frente .conteudo {
                padding: 10px 8px;
            }

            .frente .numero-cartao {
                font-size: 0.55rem;
                gap: 3px;
            }

            .frente .numero-cartao span {
                padding: 1px 3px;
            }

            .frente .info-item p {
                font-size: 0.5rem;
            }

            .frente .info-item label {
                font-size: 0.35rem;
            }

            .frente .chip {
                width: 22px;
                height: 18px;
                font-size: 0.6rem;
            }

            .frente .bandeira {
                font-size: 0.4rem;
            }

            .frente .bandeira i {
                font-size: 0.7rem;
            }

            .verso .verso-detalhes .qr-code {
                width: 50px;
                height: 50px;
            }

            .botoes .btn {
                font-size: 0.55rem;
                padding: 6px 8px;
                min-width: 40px;
            }

            .header h1 {
                font-size: 0.85rem;
            }

            .frente .badge-valid {
                font-size: 0.35rem;
                padding: 1px 6px;
                top: 6px;
                right: 6px;
            }

            .verso .verso-texto {
                font-size: 0.4rem;
            }

            .verso .verso-texto .igreja-nome {
                font-size: 0.5rem;
            }

            .verso .verso-texto .igreja-endereco,
            .verso .verso-texto .igreja-contato {
                font-size: 0.35rem;
            }

            .verso .verso-info {
                padding: 0 6px 6px;
            }

            .verso .tarja-magnetica {
                height: 20px;
                margin-top: 10px;
                margin-bottom: 6px;
            }

            .verso .linha-assinatura {
                height: 18px;
            }

            .verso .codigo-seguranca span {
                font-size: 0.5rem;
                padding: 1px 6px;
            }
        }

        /* Telas pequenas (321px - 480px) */
        @media (min-width: 321px) and (max-width: 480px) {
            .frente .conteudo {
                padding: 14px 12px;
            }

            .frente .numero-cartao {
                font-size: 0.7rem;
                gap: 4px;
            }

            .frente .numero-cartao span {
                padding: 2px 4px;
            }

            .frente .info-item p {
                font-size: 0.65rem;
            }

            .frente .info-item label {
                font-size: 0.4rem;
            }

            .frente .chip {
                width: 30px;
                height: 22px;
                font-size: 0.8rem;
            }

            .verso .verso-detalhes {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .verso .verso-detalhes .qr-code {
                width: 70px;
                height: 70px;
            }

            .verso .verso-texto {
                text-align: center;
            }

            .verso .verso-texto .igreja-endereco,
            .verso .verso-texto .igreja-contato {
                justify-content: center;
            }

            .botoes .btn {
                font-size: 0.65rem;
                padding: 8px 10px;
                min-width: 50px;
            }

            .botoes {
                gap: 6px;
            }
        }

        /* Telas médias (481px - 768px) */
        @media (min-width: 481px) and (max-width: 768px) {
            .frente .conteudo {
                padding: 18px 16px;
            }

            .frente .numero-cartao {
                font-size: 0.9rem;
                gap: 8px;
            }

            .frente .info-item p {
                font-size: 0.8rem;
            }

            .verso .verso-detalhes {
                gap: 14px;
            }

            .botoes .btn {
                font-size: 0.75rem;
                padding: 10px 16px;
                min-width: 70px;
            }
        }

        /* Telas grandes (769px - 1024px) */
        @media (min-width: 769px) and (max-width: 1024px) {
            .container {
                max-width: 480px;
            }
        }

        /* Telas extra grandes (acima de 1024px) */
        @media (min-width: 1025px) {
            .container {
                max-width: 520px;
            }

            .botoes .btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
            }

            .botoes .btn:active {
                transform: scale(0.96);
            }
        }

        /* Orientação paisagem em dispositivos móveis */
        @media (max-height: 500px) and (orientation: landscape) {
            body {
                padding: 8px 15px;
                align-items: flex-start;
                padding-top: 10px;
            }

            .container {
                max-width: 80%;
            }

            .header {
                margin-bottom: 8px;
            }

            .header h1 {
                font-size: 0.9rem;
            }

            .header p {
                display: none;
            }

            .cartao-wrapper {
                max-height: 65vh;
                margin-bottom: 8px;
                max-width: 380px;
            }

            .frente .conteudo {
                padding: 10px 14px;
            }

            .frente .numero-cartao {
                font-size: 0.7rem;
                gap: 4px;
            }

            .frente .info-item p {
                font-size: 0.6rem;
            }

            .frente .chip {
                width: 28px;
                height: 20px;
                font-size: 0.7rem;
            }

            .botoes .btn {
                font-size: 0.55rem;
                padding: 5px 10px;
                min-width: 45px;
            }

            .verso .verso-detalhes .qr-code {
                width: 55px;
                height: 55px;
            }

            .verso .verso-texto {
                font-size: 0.4rem;
            }

            .verso .verso-texto .igreja-nome {
                font-size: 0.5rem;
            }

            .verso .verso-texto .igreja-endereco,
            .verso .verso-texto .igreja-contato {
                font-size: 0.35rem;
            }

            .botoes {
                gap: 4px;
            }
        }

        /* ============================================================
           IMPRESSÃO
           ============================================================ */
        @media print {
            .botoes,
            .header,
            .toast-copiado {
                display: none !important;
            }

            .container {
                max-width: 100%;
                padding: 0;
                margin: 0;
            }

            .cartao-wrapper {
                perspective: none;
                margin: 0 auto;
                aspect-ratio: 1.58 / 1;
                width: 100%;
                max-width: 100%;
                max-height: none;
            }

            .cartao {
                transform: none !important;
                height: 100%;
                width: 100%;
            }

            .frente,
            .verso {
                position: absolute;
                backface-visibility: visible;
                border-radius: 16px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            }

            .verso {
                transform: none !important;
                display: block;
                position: absolute;
                top: 0;
                left: 0;
                opacity: 0;
                pointer-events: none;
            }

            .cartao.virado .frente {
                opacity: 0;
            }

            .cartao.virado .verso {
                opacity: 1;
                transform: none !important;
                position: absolute;
                top: 0;
                left: 0;
            }

            body {
                background: white;
                padding: 10px;
            }

            .frente .badge-valid {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-id-card" style="color: #d4af37;"></i>
                Cartão de Membro
            </h1>
            <p>Clique no cartão para virar • Apresente para validar sua identidade</p>
        </div>

        <!-- Toast de Copiado -->
        <div class="toast-copiado" id="toastCopiado">
            <i class="fas fa-check-circle"></i> Dados copiados para a área de transferência!
        </div>

        <!-- ============================================================
        CARTÃO 3D
        ============================================================ -->
        <div class="cartao-wrapper">
            <div class="cartao" id="cartao">

                <!-- ===== FRENTE ===== -->
                <div class="frente">
                    <span class="badge-valid {{ strtolower($membro->status) == 'ativo' ? '' : 'inativo' }}">
                        <i class="fas fa-circle" style="font-size: 0.4rem; margin-right: 4px;"></i>
                        {{ strtoupper($membro->status) }}
                    </span>

                    <div class="conteudo">
                        <!-- Header -->
                        <div class="header-cartao">
                            <div class="bandeira">
                                <i class="fas fa-cross"></i>
                                <div>
                                    <div style="font-size: clamp(0.35rem, 0.9vw, 0.6rem); opacity: 0.5; letter-spacing: 2px;">IGREJA</div>
                                    <div class="logo-texto">
                                        CENTRAL
                                        <span>MEMBRO OFICIAL</span>
                                    </div>
                                </div>
                            </div>
                            <div class="chip">
                                <i class="fas fa-microchip"></i>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="body-cartao">
                            <div class="numero-cartao">
                                <span>****</span>
                                <span>****</span>
                                <span>****</span>
                                <span>{{ str_pad($membro->matricula, 6, '0', STR_PAD_LEFT) }}</span>
                            </div>

                            <div class="info-cartao">
                                <div class="info-item">
                                    <label>NOME DO MEMBRO</label>
                                    <p>{{ $membro->nome }}</p>
                                </div>
                                <div class="info-item validade">
                                    <label>VÁLIDO ATÉ</label>
                                    <p>
                                        @php
                                            $validade = $membro->datCadastro ? date('m/y', strtotime('+5 years', strtotime($membro->datCadastro))) : '12/28';
                                        @endphp
                                        {{ $validade }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="footer-cartao">
                            <div class="status-membro">
                                <i class="fas fa-circle" style="color: {{ strtolower($membro->status) == 'ativo' ? '#4CAF50' : '#f44336' }};"></i>
                                <span>{{ strtoupper($membro->status) }}</span>
                            </div>
                            <div class="cargo-membro">
                                <i class="fas fa-user-tie"></i>
                                <span>{{ $membro->funcao }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== VERSO ===== -->
                <div class="verso">
                    <div class="verso-conteudo">
                        <div class="tarja-magnetica"></div>

                        <div class="verso-info">
                            <!-- Assinatura e CVV -->
                            <div class="assinatura-area">
                                <div class="linha-assinatura"></div>
                                <div class="codigo-seguranca">
                                    <label>CVV</label>
                                    <span>{{ substr(str_pad($membro->matricula, 6, '0'), -3) }}</span>
                                </div>
                            </div>

                            <!-- QR Code e informações -->
                            <div class="verso-detalhes">
                                <div class="qr-code" id="qrCode"></div>

                                <div class="verso-texto">
                                    <span class="igreja-nome">
                                        <i class="fas fa-cross" style="font-size: clamp(0.4rem, 0.9vw, 0.6rem);"></i> IGREJA CENTRAL
                                    </span>
                                    <span class="igreja-endereco">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $membro->cidade }}/{{ $membro->uf }}
                                    </span>
                                    <span class="igreja-contato">
                                        <i class="fas fa-phone"></i>
                                        {{ $membro->telefone ?? '(11) 99999-9999' }}
                                    </span>
                                    @if($membro->email)
                                    <span class="igreja-contato">
                                        <i class="fas fa-envelope"></i>
                                        {{ $membro->email }}
                                    </span>
                                    @endif

                                    <div class="info-extra">
                                        <i class="fas fa-id-card"></i> Matrícula: {{ $membro->matricula }}
                                        &bull; <i class="fas fa-calendar-alt"></i> Ing: {{ date('d/m/Y', strtotime($membro->datCadastro)) }}
                                        @if($membro->dataBatismo)
                                        &bull; <i class="fas fa-water"></i> Bat: {{ date('d/m/Y', strtotime($membro->dataBatismo)) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================
        BOTÕES
        ============================================================ -->
        <div class="botoes">
            <a href="{{ route('feed') }}" class="btn btn-voltar">
                <i class="fas fa-arrow-left"></i> <span class="btn-text">Voltar</span>
            </a>

            <button class="btn btn-girar" id="virarCartao">
                <i class="fas fa-sync-alt"></i> <span class="btn-text" id="btnVirarTexto">Virar</span>
            </button>

            <button class="btn btn-whatsapp" id="compartilharWhatsApp">
                <i class="fab fa-whatsapp"></i> <span class="btn-text">WhatsApp</span>
            </button>

            <button class="btn btn-imprimir" onclick="window.print()">
                <i class="fas fa-print"></i> <span class="btn-text">Imprimir</span>
            </button>

            <button class="btn btn-copiar" id="copiarDados">
                <i class="fas fa-copy"></i> <span class="btn-text">Copiar</span>
            </button>
        </div>
    </div>

    <!-- ============================================================
    JAVASCRIPT - TODAS AS FUNCIONALIDADES
    ============================================================ -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ============================================================
            // DADOS DO MEMBRO (do Blade)
            // ============================================================
            const membro = {
                matricula: "{{ $membro->matricula }}",
                nome: "{{ $membro->nome }}",
                funcao: "{{ $membro->funcao }}",
                status: "{{ strtoupper($membro->status) }}",
                email: "{{ $membro->email }}",
                telefone: "{{ $membro->telefone }}",
                cidade: "{{ $membro->cidade }}",
                uf: "{{ $membro->uf }}",
                congregacao: "{{ $membro->congregacao }}",
                datCadastro: "{{ date('d/m/Y', strtotime($membro->datCadastro)) }}",
                dataBatismo: "{{ $membro->dataBatismo ? date('d/m/Y', strtotime($membro->dataBatismo)) : 'Não informado' }}",
                dataConsagracao: "{{ $membro->data_Consagracao ? date('d/m/Y', strtotime($membro->data_Consagracao)) : 'Não informado' }}"
            };

            // ============================================================
            // 1. GERAR QR CODE
            // ============================================================
            const qrContainer = document.getElementById('qrCode');
            if (qrContainer) {
                const qrData = `ID:${membro.matricula}|${membro.nome}`;

                new QRCode(qrContainer, {
                    text: qrData,
                    width: 90,
                    height: 90,
                    colorDark: '#1a1a2e',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.L
                });
            }

            // ============================================================
            // 2. VIRAR CARTÃO (COM EIXO CENTRALIZADO)
            // ============================================================
            const cartao = document.getElementById('cartao');
            const btnVirar = document.getElementById('virarCartao');
            const btnVirarTexto = document.getElementById('btnVirarTexto');
            let virado = false;

            function virarCartao() {
                virado = !virado;
                cartao.classList.toggle('virado');

                const icone = btnVirar.querySelector('i');
                if (virado) {
                    icone.className = 'fas fa-undo-alt';
                    btnVirarTexto.textContent = 'Verso';
                } else {
                    icone.className = 'fas fa-sync-alt';
                    btnVirarTexto.textContent = 'Virar';
                }
            }

            btnVirar.addEventListener('click', function(e) {
                e.stopPropagation();
                virarCartao();
            });

            cartao.addEventListener('click', function(e) {
                if (e.target.closest('.qr-code') || e.target.closest('.btn')) {
                    return;
                }
                virarCartao();
            });

            // ============================================================
            // 3. COMPARTILHAR WHATSAPP
            // ============================================================
            document.getElementById('compartilharWhatsApp').addEventListener('click', function() {
                const mensagem =
                    `🕊️ *CARTÃO DE MEMBRO DIGITAL*\n\n` +
                    `🏛️ Igreja Central\n` +
                    `━━━━━━━━━━━━━━━━━━━\n` +
                    `👤 Nome: ${membro.nome}\n` +
                    `📌 Função: ${membro.funcao}\n` +
                    `🆔 Matrícula: ${membro.matricula}\n` +
                    `✅ Status: ${membro.status}\n` +
                    `🏠 Congregação: ${membro.congregacao || 'Igreja Central'}\n` +
                    `📅 Cadastro: ${membro.datCadastro}\n` +
                    `━━━━━━━━━━━━━━━━━━━\n` +
                    `📱 Apresente este cartão para validação.`;

                const url = `https://wa.me/?text=${encodeURIComponent(mensagem)}`;
                window.open(url, '_blank');
            });

            // ============================================================
            // 4. COPIAR DADOS
            // ============================================================
            document.getElementById('copiarDados').addEventListener('click', function() {
                const dados =
                    `CARTÃO DE MEMBRO - IGREJA CENTRAL\n` +
                    `━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n` +
                    `Nome: ${membro.nome}\n` +
                    `Matrícula: ${membro.matricula}\n` +
                    `Função: ${membro.funcao}\n` +
                    `Status: ${membro.status}\n` +
                    `Email: ${membro.email || 'Não informado'}\n` +
                    `Telefone: ${membro.telefone || 'Não informado'}\n` +
                    `Cidade/UF: ${membro.cidade}/${membro.uf}\n` +
                    `Congregação: ${membro.congregacao || 'Igreja Central'}\n` +
                    `Data Cadastro: ${membro.datCadastro}\n` +
                    `Data Batismo: ${membro.dataBatismo}\n` +
                    `Data Consagração: ${membro.dataConsagracao}\n` +
                    `━━━━━━━━━━━━━━━━━━━━━━━━━━━━`;

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(dados).then(() => mostrarToast())
                        .catch(() => fallbackCopiar(dados));
                } else {
                    fallbackCopiar(dados);
                }
            });

            function fallbackCopiar(texto) {
                const textarea = document.createElement('textarea');
                textarea.value = texto;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                textarea.remove();
                mostrarToast();
            }

            // ============================================================
            // 5. TOAST DE CONFIRMAÇÃO
            // ============================================================
            function mostrarToast() {
                const toast = document.getElementById('toastCopiado');
                toast.style.display = 'block';
                clearTimeout(toast._timeout);
                toast._timeout = setTimeout(() => {
                    toast.style.display = 'none';
                }, 3000);
            }

            document.getElementById('toastCopiado').addEventListener('click', function() {
                this.style.display = 'none';
            });

            // ============================================================
            // 6. RESTAURAR ESTADO AO IMPRIMIR
            // ============================================================
            window.addEventListener('beforeprint', function() {
                if (virado) {
                    virarCartao();
                }
            });

            console.log('✅ Cartão de Membro carregado com sucesso!');
            console.log(`👤 Membro: ${membro.nome} (${membro.matricula})`);
        });
    </script>
</body>
</html>