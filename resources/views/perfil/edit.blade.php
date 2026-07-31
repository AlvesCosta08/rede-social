@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('styles')
<style>
    .edit-container { max-width: 600px; margin: 0 auto; }
    .edit-form { background: var(--card-bg); border-radius: 16px; padding: 30px; box-shadow: var(--shadow); border: 1px solid var(--border-color); }
    .edit-form .form-group { margin-bottom: 18px; }
    .edit-form label { display: block; font-weight: 600; margin-bottom: 5px; font-size: 0.9rem; }
    .edit-form input, .edit-form textarea {
        width: 100%; padding: 10px 14px;
        border: 2px solid var(--border-color); border-radius: 10px;
        background: var(--bg); color: var(--text); font-size: 0.95rem;
        transition: 0.3s;
    }
    .edit-form input:focus, .edit-form textarea:focus { outline: none; border-color: var(--destaque); }
    .edit-form textarea { min-height: 100px; resize: vertical; font-family: inherit; }
    .btn-submit { padding: 12px 30px; background: var(--destaque); border: none; border-radius: 10px; color: #1a1a2e; font-weight: 700; cursor: pointer; transition: 0.3s; }
    .btn-submit:hover { transform: scale(1.05); box-shadow: 0 4px 15px rgba(201, 168, 76, 0.3); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .btn-voltar { padding: 12px 30px; background: #e74c3c; border: none; border-radius: 10px; color: white; font-weight: 700; cursor: pointer; transition: 0.3s; text-decoration: none; display: inline-block; }
    .btn-voltar:hover { transform: scale(1.05); }
    .foto-upload { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
    .foto-upload .preview { width: 80px; height: 80px; border-radius: 50%; background: var(--destaque); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #1a1a2e; overflow: hidden; }
    .foto-upload .preview img { width: 100%; height: 100%; object-fit: cover; }
    .foto-upload input[type="file"] { padding: 8px; border: 2px dashed var(--border-color); border-radius: 10px; flex: 1; background: var(--bg); color: var(--text); }
    @media (max-width: 480px) { .form-row { grid-template-columns: 1fr; } .foto-upload { flex-direction: column; align-items: stretch; } }
</style>
@endsection

@section('content')
<div class="edit-container">
    <h2 style="margin-bottom: 20px;"><i class="fas fa-user-edit" style="color: var(--destaque);"></i> Editar Perfil</h2>

    <div class="edit-form">
        <form method="POST" action="{{ route('perfil.update') }}">
            @csrf @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" value="{{ $membro->nome }}" required>
                </div>
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="telefone" value="{{ $membro->telefone }}" required>
                </div>
            </div>

            <div class="form-group">
                <label>Endereço</label>
                <input type="text" name="endereco" value="{{ $membro->endereco }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Cidade</label>
                    <input type="text" name="cidade" value="{{ $membro->cidade }}" required>
                </div>
                <div class="form-group">
                    <label>UF</label>
                    <select name="uf" required>
                        @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                            <option value="{{ $uf }}" {{ $membro->uf == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Bio (sobre você)</label>
                <textarea name="bio" placeholder="Compartilhe um pouco sobre sua fé, família e hobbies...">{{ $membro->bio }}</textarea>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="privacidade" {{ $membro->privacidade ? 'checked' : '' }}>
                    Perfil privado (apenas membros podem ver)
                </label>
            </div>

            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Salvar Alterações</button>
                <a href="{{ route('perfil.show', $membro->matricula) }}" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar</a>
            </div>
        </form>

        <hr style="margin: 25px 0; border-color: var(--border-color);">

        <h3><i class="fas fa-camera"></i> Foto de Perfil</h3>
        <form method="POST" action="{{ route('perfil.foto') }}" enctype="multipart/form-data">
            @csrf
            <div class="foto-upload">
                <div class="preview">
                    @if($membro->foto)
                        <img src="{{ asset('storage/fotos/' . $membro->foto) }}" alt="Foto">
                    @else
                        {{ substr($membro->nome, 0, 1) }}
                    @endif
                </div>
                <input type="file" name="foto" accept="image/*" required>
                <button type="submit" class="btn-submit" style="padding: 8px 20px; font-size: 0.9rem;"><i class="fas fa-upload"></i> Enviar</button>
            </div>
        </form>
    </div>
</div>
@endsection