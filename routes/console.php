<?php

use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\LimparDadosOrfaos;
use App\Console\Commands\SincronizarUsuarios;

// ============================================================
// TAREFAS AGENDADAS - EXECUTADAS AUTOMATICAMENTE
// ============================================================

// 1. LIMPAR DADOS ÓRFÃOS - Diariamente às 3h
Schedule::command('dados:limpar-orfaos --force')->dailyAt('03:00');

// 2. SINCRONIZAR USUÁRIOS - A cada hora (mantém atualizado)
Schedule::command('usuarios:sincronizar --force')->hourly();

// 3. OU a cada 6 horas (menos frequente)
// Schedule::command('usuarios:sincronizar --force')->everySixHours();

// 4. OU diariamente (se tiver poucos usuários novos)
// Schedule::command('usuarios:sincronizar --force')->dailyAt('04:00');

// 5. OU a cada minuto (para testes - NÃO USAR EM PRODUÇÃO)
// Schedule::command('usuarios:sincronizar --force')->everyMinute();
