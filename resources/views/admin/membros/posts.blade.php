@extends('layouts.app')

@section('title', 'Posts de ' . $membro->nome . ' - Admin')

@section('page-title', 'Posts de ' . $membro->nome)
@section('page-subtitle', 'Gerenciar publicações do membro')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('admin.membros.index') }}" class="hover:text-indigo-500 transition">
            <i class="fas fa-users-cog"></i> Membros
        </a>
        <span>/</span>
        <a href="{{ route('admin.membros.show', $membro->matricula) }}" class="hover:text-indigo-500 transition">
            {{ $membro->nome }}
        </a>
        <span>/</span>
        <span class="text-gray-700 dark:text-gray-300 font-medium">Posts</span>
    </div>

    <!-- Informações do membro -->
    <div class="card-modern mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full overflow-hidden bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-2xl font-bold">
                @if($membro->foto)
                    <img src="{{ $membro->foto_url }}" alt="{{ $membro->nome }}" class="w-full h-full object-cover">
                @else
                    {{ substr($membro->nome, 0, 1) }}
                @endif
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $membro->nome }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-medium">Matrícula:</span> {{ $membro->matricula }} •
                    <span class="font-medium">Função:</span> {{ $membro->funcao ?? 'Membro' }} •
                    <span class="font-medium">Status:</span>
                    <span class="px-2 py-0.5 text-xs rounded-full 
                        {{ $membro->status === 'ativo' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                        {{ ucfirst($membro->status) }}
                    </span>
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    <i class="fas fa-file-alt"></i> 
                    {{ $publicacoes->total() }} publicações • 
                    <i class="fas fa-calendar-alt ml-2"></i> 
                    Membro desde: {{ $membro->datCadastro ? $membro->datCadastro->format('d/m/Y') : 'N/A' }}
                </p>
            </div>
            <div class="ml-auto flex gap-2">
                <a href="{{ route('admin.membros.show', $membro->matricula) }}" 
                   class="btn-modern btn-modern-outline text-sm">
                    <i class="fas fa-user"></i> Perfil
                </a>
                <a href="{{ route('admin.membros.edit', $membro->matricula) }}" 
                   class="btn-modern btn-modern-primary text-sm">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        </div>
    </div>

    <!-- Ações em massa -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <button onclick="selecionarTodos()" 
                    class="text-sm text-gray-500 hover:text-indigo-500 transition">
                <i class="fas fa-check-double"></i> Selecionar Todos
            </button>
            <button onclick="desmarcarTodos()" 
                    class="text-sm text-gray-500 hover:text-indigo-500 transition">
                <i class="fas fa-times"></i> Desmarcar
            </button>
        </div>
        <button onclick="deletarSelecionados()" 
                class="btn-modern btn-modern-danger text-sm">
            <i class="fas fa-trash"></i> Deletar Selecionados
        </button>
    </div>

    <!-- Lista de publicações -->
    @if($publicacoes->count() > 0)
        <div class="space-y-3">
            @foreach($publicacoes as $publicacao)
                <div class="card-modern hover:border-indigo-200 transition">
                    <div class="flex items-start gap-4">
                        <!-- Checkbox para seleção -->
                        <input type="checkbox" 
                               class="post-checkbox mt-2 w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                               value="{{ $publicacao->id }}">
                        
                        <div class="flex-1 min-w-0">
                            <!-- Cabeçalho -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full overflow-hidden bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                        @if($publicacao->autor && $publicacao->autor->foto)
                                            <img src="{{ $publicacao->autor->foto_url }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            {{ $publicacao->autor ? substr($publicacao->autor->nome, 0, 1) : '?' }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-white text-sm">
                                            {{ $publicacao->autor ? $publicacao->autor->nome : 'Usuário' }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $publicacao->created_at->format('d/m/Y H:i') }} • 
                                            {{ $publicacao->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Badges -->
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400">
                                        <i class="fas fa-heart text-red-400"></i> 
                                        {{ $publicacao->curtidas->count() }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        <i class="fas fa-comment text-blue-400"></i> 
                                        {{ $publicacao->comentarios->count() }}
                                    </span>
                                    <button onclick="deletarPublicacao({{ $publicacao->id }})" 
                                            class="text-gray-400 hover:text-red-500 transition text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Conteúdo -->
                            <div class="mt-2">
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap text-sm">
                                    {{ $publicacao->conteudo }}
                                </p>
                            </div>

                            <!-- Comentários (expandir) -->
                            @if($publicacao->comentarios->count() > 0)
                                <button onclick="toggleComentarios({{ $publicacao->id }})" 
                                        class="mt-2 text-xs text-gray-400 hover:text-indigo-500 transition">
                                    <i class="fas fa-comment"></i> 
                                    Ver {{ $publicacao->comentarios->count() }} comentários
                                </button>
                                
                                <div id="comentarios-admin-{{ $publicacao->id }}" class="mt-2 hidden">
                                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 space-y-2">
                                        @foreach($publicacao->comentarios as $comentario)
                                            <div class="flex items-start gap-2">
                                                <div class="w-6 h-6 rounded-full overflow-hidden bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                                    @if($comentario->autor && $comentario->autor->foto)
                                                        <img src="{{ $comentario->autor->foto_url }}" alt="" class="w-full h-full object-cover">
                                                    @else
                                                        {{ $comentario->autor ? substr($comentario->autor->nome, 0, 1) : '?' }}
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
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
                                                <button onclick="deletarComentario({{ $comentario->id }})" 
                                                        class="text-gray-400 hover:text-red-500 transition text-xs">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
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
                {{ $membro->nome }} ainda não fez nenhuma publicação.
            </p>
        </div>
    @endif
</div>

<script>
    // ============================================================
    // FUNÇÕES ADMIN
    // ============================================================

    function selecionarTodos() {
        document.querySelectorAll('.post-checkbox').forEach(cb => cb.checked = true);
    }

    function desmarcarTodos() {
        document.querySelectorAll('.post-checkbox').forEach(cb => cb.checked = false);
    }

    function deletarSelecionados() {
        const selecionados = document.querySelectorAll('.post-checkbox:checked');
        
        if (selecionados.length === 0) {
            alert('Selecione pelo menos uma publicação para deletar.');
            return;
        }

        if (!confirm(`Tem certeza que deseja deletar ${selecionados.length} publicação(ões)?`)) {
            return;
        }

        const ids = Array.from(selecionados).map(cb => cb.value);
        
        // Deleta uma por uma (ou pode fazer em lote)
        let deletados = 0;
        ids.forEach(id => {
            fetch(`/admin/posts/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    deletados++;
                    // Remove o elemento da página
                    const elemento = document.querySelector(`input[value="${id}"]`)?.closest('.card-modern');
                    if (elemento) {
                        elemento.style.display = 'none';
                    }
                    
                    if (deletados === ids.length) {
                        alert(`${deletados} publicação(ões) deletada(s) com sucesso!`);
                        window.location.reload();
                    }
                }
            })
            .catch(error => console.error('Erro:', error));
        });
    }

    function deletarPublicacao(id) {
        if (!confirm('Tem certeza que deseja deletar esta publicação?')) return;

        fetch(`/admin/posts/${id}`, {
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

        // Comentários são deletados via FeedController
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

    function toggleComentarios(id) {
        const div = document.getElementById(`comentarios-admin-${id}`);
        if (div) {
            div.classList.toggle('hidden');
        }
    }

    console.log('👑 Admin - Página de posts carregada');
    console.log('👤 Membro: {{ $membro->nome }}');
    console.log('📄 Total de publicações: {{ $publicacoes->total() }}');
</script>
@endsection