<?php

namespace App\Repositories;

use App\Models\Filiado;
use App\Contracts\Repositories\MembroRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MembroRepository implements MembroRepositoryInterface
{
    protected $model;

    public function __construct(Filiado $model)
    {
        $this->model = $model;
    }

    public function findById(string $matricula): ?object
    {
        return $this->model->on('mysql')->where('matricula', $matricula)->first();
    }

    public function findByEmail(string $email): ?object
    {
        return $this->model->on('mysql')->where('email', $email)->first();
    }

    public function findByDocumento(string $documento): ?object
    {
        return $this->model->on('mysql')->where('documento', $documento)->first();
    }

    public function search(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->on('mysql');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nome', 'LIKE', "%{$search}%")
                  ->orWhere('matricula', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('telefone', 'LIKE', "%{$search}%")
                  ->orWhere('cidade', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['funcao'])) {
            $query->where('funcao', $filters['funcao']);
        }

        if (!empty($filters['cidade'])) {
            $query->where('cidade', 'LIKE', "%{$filters['cidade']}%");
        }

        if (!empty($filters['congregacao'])) {
            $query->where('congregacao', 'LIKE', "%{$filters['congregacao']}%");
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', '!=', 'saida');
        }

        $ordenar = $filters['ordenar'] ?? 'nome';
        $direcao = $filters['direcao'] ?? 'asc';
        $query->orderBy($ordenar, $direcao);

        return $query->paginate($perPage);
    }

    public function create(array $data): object
    {
        return $this->model->on('mysql')->create($data);
    }

    public function update(string $matricula, array $data): bool
    {
        return $this->model->on('mysql')
            ->where('matricula', $matricula)
            ->update($data);
    }

    public function delete(string $matricula): bool
    {
        $membro = $this->findById($matricula);
        if (!$membro) {
            return false;
        }
        return $membro->delete();
    }

    public function getActiveMembers(): array
    {
        return $this->model->on('mysql')
            ->whereIn('status', ['ativo', 'membro'])
            ->orderBy('nome')
            ->get()
            ->toArray();
    }

    public function getMembersByCongregacao(string $congregacao): array
    {
        return $this->model->on('mysql')
            ->where('congregacao', $congregacao)
            ->whereIn('status', ['ativo', 'membro'])
            ->orderBy('nome')
            ->get()
            ->toArray();
    }

    public function getMembersByFuncao(string $funcao): array
    {
        return $this->model->on('mysql')
            ->where('funcao', $funcao)
            ->whereIn('status', ['ativo', 'membro'])
            ->orderBy('nome')
            ->get()
            ->toArray();
    }

    public function getStatistics(): array
    {
        $total = $this->model->on('mysql')->count();
        $ativos = $this->model->on('mysql')->whereIn('status', ['ativo', 'membro'])->count();
        $inativos = $this->model->on('mysql')->where('status', 'inativo')->count();
        $transferidos = $this->model->on('mysql')->where('status', 'transferido')->count();
        $saida = $this->model->on('mysql')->where('status', 'saida')->count();

        $porFuncao = $this->model->on('mysql')
            ->selectRaw('funcao, count(*) as total')
            ->whereIn('status', ['ativo', 'membro'])
            ->groupBy('funcao')
            ->orderBy('total', 'desc')
            ->get()
            ->toArray();

        $porCongregacao = $this->model->on('mysql')
            ->selectRaw('congregacao, count(*) as total')
            ->whereIn('status', ['ativo', 'membro'])
            ->whereNotNull('congregacao')
            ->groupBy('congregacao')
            ->orderBy('total', 'desc')
            ->get()
            ->toArray();

        return [
            'total' => $total,
            'ativos' => $ativos,
            'inativos' => $inativos,
            'transferidos' => $transferidos,
            'saida' => $saida,
            'por_funcao' => $porFuncao,
            'por_congregacao' => $porCongregacao,
        ];
    }

    public function getAniversariantesDoMes(): array
    {
        $mes = Carbon::now()->month;
        $dia = Carbon::now()->day;

        return $this->model->on('mysql')
            ->whereMonth('dataNascimento', $mes)
            ->whereDay('dataNascimento', '>=', $dia)
            ->whereIn('status', ['ativo', 'membro'])
            ->orderByRaw('DAY(dataNascimento)')
            ->limit(20)
            ->get()
            ->toArray();
    }
}