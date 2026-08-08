<?php

namespace App\Repositories;

use App\Models\Publicacao;
use App\Models\Curtida;
use App\Models\Comentario;
use App\Contracts\Repositories\PublicacaoRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PublicacaoRepository implements PublicacaoRepositoryInterface
{
    protected $model;

    public function __construct(Publicacao $model)
    {
        $this->model = $model;
    }

    public function findById(int $id): ?object
    {
        return $this->model->on('mysql')
            ->with(['autor', 'comentarios.autor', 'curtidas'])
            ->find($id);
    }

    public function getFeed(array $seguindoIds, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->on('mysql')
            ->with(['autor', 'comentarios.autor', 'curtidas'])
            ->whereIn('filiado_matricula', $seguindoIds)
            ->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    public function getGlobalFeed(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->on('mysql')
            ->with(['autor', 'comentarios.autor', 'curtidas'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByUser(string $matricula, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->on('mysql')
            ->where('filiado_matricula', $matricula)
            ->with(['autor', 'comentarios.autor', 'curtidas'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): object
    {
        return $this->model->on('mysql')->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $publicacao = $this->model->on('mysql')->find($id);
        if (!$publicacao) {
            return false;
        }
        return $publicacao->update($data);
    }

    public function delete(int $id): bool
    {
        $publicacao = $this->model->on('mysql')->find($id);
        if (!$publicacao) {
            return false;
        }

        DB::beginTransaction();
        try {
            Comentario::on('mysql')->where('publicacao_id', $id)->delete();
            Curtida::on('mysql')->where('publicacao_id', $id)->delete();
            $publicacao->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function curtir(int $publicacaoId, string $matricula): array
    {
        $jaCurtiu = Curtida::on('mysql')
            ->where('publicacao_id', $publicacaoId)
            ->where('filiado_matricula', $matricula)
            ->exists();

        if ($jaCurtiu) {
            Curtida::on('mysql')
                ->where('publicacao_id', $publicacaoId)
                ->where('filiado_matricula', $matricula)
                ->delete();
            $curtido = false;
        } else {
            Curtida::on('mysql')->create([
                'publicacao_id' => $publicacaoId,
                'filiado_matricula' => $matricula,
                'created_at' => now()
            ]);
            $curtido = true;
        }

        $totalCurtidas = Curtida::on('mysql')
            ->where('publicacao_id', $publicacaoId)
            ->count();

        return [
            'curtido' => $curtido,
            'total' => $totalCurtidas
        ];
    }

    public function comentar(int $publicacaoId, string $matricula, string $conteudo): object
    {
        return Comentario::on('mysql')->create([
            'publicacao_id' => $publicacaoId,
            'filiado_matricula' => $matricula,
            'conteudo' => $conteudo,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}