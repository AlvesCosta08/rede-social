@extends('layouts.app')

@section('title', 'Posts de ' . $perfil->nome)

@section('page-title', 'Posts de ' . $perfil->nome)
@section('page-subtitle', 'Todas as publicações de ' . $perfil->nome)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Perfil do usuário -->
    <div class="card-modern mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full overflow-hidden bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-2xl font-bold">
                @php
                    $fotoNome = $perfil->foto ?? null;
                    $fotoUrl = $fotoNome ? route('imagem.foto', ['filename' => $fotoNome]) : null;
                    $inicial = substr($perfil->nome, 0, 1);
                @endphp
                
                @if($fotoUrl)
                    <img src="{{ $fotoUrl }}" 
                         alt="{{ $perfil->nome }}" 
                         class="w-full h-full object-cover"
                         onerror="this.style.display='none'; this.parentElement.textContent='{{ $inicial }}';">
                @else
                    {{ $inicial }}
                @endif
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $perfil->nome }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $perfil->funcao ?? 'Membro' }} • 
                    {{ $perfil->cidade ?? '' }}{{ $perfil->uf ? ' - ' . $perfil->uf : '' }}
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    <i class="fas fa-file-alt"></i> 
                    {{ $publicacoes->total() }} publicações
                </p>
            </div>
            <div class="ml-auto">
                <a href="{{ route('perfil.show', $perfil->matricula) }}" 
                   class="btn-modern btn-modern-outline text-sm">
                    <i class="fas fa-arrow-left"></i> Voltar ao perfil
                </a>
            </div>
        </div>
    </div>

    <!-- Lista de publicações -->
    @if($publicacoes->count() > 0)
        <div class="space-y-4">
            @foreach($publicacoes as $publicacao)
                <div class="card-modern">
                    <!-- Cabeçalho da publicação -->
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm">
                                @php
                                    $autorFotoNome = $publicacao->autor->foto ?? null;
                                    $autorFotoUrl = $autorFotoNome ? route('imagem.foto', ['filename' => $autorFotoNome]) : null;
                                    $autorInicial = $publicacao->autor ? substr($publicacao->autor->nome, 0, 1) : '?';
                                @endphp
                                
                                @if($autorFotoUrl)
                                    <img src="{{ $autorFotoUrl }}" 
                                         alt="{{ $publicacao->autor->nome ?? 'Usuário' }}" 
                                         class="w-full h-full object-cover"
                                         onerror="this.style.display='none'; this.parentElement.textContent='{{ $autorInicial }}';">
                                @else
                                    {{ $autorInicial }}
                                @endif
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white">
                                    {{ $publicacao->autor ? $publicacao->autor->nome : 'Usuário' }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $publicacao->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        
                        {{-- ⭐ DELETAR: PRÓPRIO USUÁRIO OU ADMIN/SECRETÁRIO --}}
                        @if(auth()->check() && (auth()->user()->matricula == $publicacao->filiado_matricula || auth()->user()->pode('excluir_membro')))
                            <div class="flex gap-2">
                                <button onclick="editarPublicacao({{ $publicacao->id }})" 
                                        class="text-gray-400 hover:text-blue-500 transition">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deletarPublicacao({{ $publicacao->id }})" 
                                        class="text-gray-400 hover:text-red-500 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Conteúdo -->
                    <div class="mt-3">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                            {{ $publicacao->conteudo }}
                        </p>
                    </div>

                    <!-- Ações -->
                    <div class="mt-4 flex items-center gap-6 border-t border-gray-100 dark:border-gray-700 pt-3">
                        <button onclick="curtir({{ $publicacao->id }})" 
                                class="flex items-center gap-2 text-sm transition hover:text-indigo-500 
                                       {{ isset($publicacao->curtida_por_mim) && $publicacao->curtida_por_mim ? 'text-indigo-500' : 'text-gray-500' }}">
                            <i class="fas fa-heart"></i>
                            <span id="curtidas-{{ $publicacao->id }}">{{ $publicacao->curtidas->count() }}</span>
                        </button>
                        
                        <button onclick="toggleComentarios({{ $publicacao->id }})" 
                                class="flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-500 transition">
                            <i class="fas fa-comment"></i>
                            <span>{{ $publicacao->comentarios->count() }}</span>
                        </button>
                    </div>

                    <!-- Comentários -->
                    <div id="comentarios-{{ $publicacao->id }}" class="mt-3 hidden">
                        <div class="space-y-2 max-h-60 overflow-y-auto">
                            @foreach($publicacao->comentarios as $comentario)
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                    <div class="flex items-start gap-2">
                                        <div class="w-6 h-6 rounded-full overflow-hidden bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            @php
                                                $comentFotoNome = $comentario->autor->foto ?? null;
                                                $comentFotoUrl = $comentFotoNome ? route('imagem.foto', ['filename' => $comentFotoNome]) : null;
                                                $comentInicial = $comentario->autor ? substr($comentario->autor->nome, 0, 1) : '?';
                                            @endphp
                                            
                                            @if($comentFotoUrl)
                                                <img src="{{ $comentFotoUrl }}" 
                                                     alt="" 
                                                     class="w-full h-full object-cover"
                                                     onerror="this.style.display='none'; this.parentElement.textContent='{{ $comentInicial }}';">
                                            @else
                                                {{ $comentInicial }}
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-sm text-gray-800 dark:text-white">
                                                    {{ $comentario->autor ? $comentario->autor->nome : 'Usuário' }}
                                                </span>
                                                <span class="text-xs text-gray-400">
                                                    {{ $comentario->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                {{ $comentario->conteudo }}
                                            </p>
                                        </div>
                                        @if(auth()->check() && (auth()->user()->matricula == $comentario->filiado_matricula || auth()->user()->pode('excluir_membro')))
                                            <button onclick="deletarComentario({{ $comentario->id }})" 
                                                    class="text-gray-400 hover:text-red-500 transition text-xs">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Formulário de comentário -->
                        <div class="mt-3 flex gap-2">
                            <input type="text" 
                                   id="comentario-input-{{ $publicacao->id }}" 
                                   placeholder="Escreva um comentário..."
                                   class="flex-1 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <button onclick="comentar({{ $publicacao->id }})" 
                                    class="btn-modern btn-modern-primary text-sm">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginação -->
        <div class="mt-6">
            {{ $publicacoes->links() }}
        </div>

    @else
        <div class="card-modern text-center py-12">
            <i class="fas fa-file-alt text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400">Nenhuma publicação</h3>
            <p class="text-gray-500 dark:text-gray-500">
                {{ $perfil->nome }} ainda não fez nenhuma publicação.
            </p>
        </div>
    @endif
</div>

<script>
    // ============================================================
    // FUNÇÕES DO FEED
    // ============================================================

    function curtir(id) {
        fetch(`/feed/${id}/curtir`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const span = document.getElementById(`curtidas-${id}`);
                if (span) span.textContent = data.total;
                
                const btn = event.target.closest('button');
                if (btn) {
                    if (data.curtido) {
                        btn.classList.add('text-indigo-500');
                    } else {
                        btn.classList.remove('text-indigo-500');
                    }
                }
            }
        })
        .catch(error => console.error('Erro:', error));
    }

    function comentar(id) {
        const input = document.getElementById(`comentario-input-${id}`);
        const conteudo = input.value.trim();
        
        if (!conteudo) {
            alert('Digite um comentário.');
            return;
        }

        fetch(`/feed/${id}/comentar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ conteudo: conteudo })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Erro ao comentar.');
            }
        })
        .catch(error => console.error('Erro:', error));
    }

    function toggleComentarios(id) {
        const div = document.getElementById(`comentarios-${id}`);
        if (div) {
            div.classList.toggle('hidden');
        }
    }

    function deletarPublicacao(id) {
        if (!confirm('Tem certeza que deseja deletar esta publicação?')) return;

        fetch(`/feed/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Erro ao deletar publicação.');
            }
        })
        .catch(error => console.error('Erro:', error));
    }

    function deletarComentario(id) {
        if (!confirm('Tem certeza que deseja deletar este comentário?')) return;

        fetch(`/feed/comentario/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Erro ao deletar comentário.');
            }
        })
        .catch(error => console.error('Erro:', error));
    }

    function editarPublicacao(id) {
        const conteudo = prompt('Edite sua publicação:');
        if (conteudo === null) return;
        
        if (!conteudo.trim()) {
            alert('A publicação não pode estar vazia.');
            return;
        }

        fetch(`/feed/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ conteudo: conteudo })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Erro ao editar publicação.');
            }
        })
        .catch(error => console.error('Erro:', error));
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const target = e.target;
            if (target && target.id && target.id.startsWith('comentario-input-')) {
                const id = target.id.replace('comentario-input-', '');
                comentar(id);
            }
        }
    });

</script>
@endsection