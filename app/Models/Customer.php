<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
  protected $fillable = [
    'ci_nit',
    'razonSocial',
    'nombre',
    'celular',
    'email',
    'direccion',
    'observacion',
    'estado',
    'created_by',
  ];

  public function Quotations()
  {
    return $this->hasMany(Quotation::class);
  }

  public function Sales()
  {
    return $this->hasMany(Sale::class);
  }

  public function User()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function hasPendingPayment()
  {
    return $this->Sales()
      ->where('estado', '1')
      ->where(function ($query) {
        $query->where('estado_pago', 'PENDIENTE')
          ->orWhere('estado_pago', 'PARCIAL');
      })
      ->exists();
  }

  public function getTotalPendingBalance()
  {
    return $this->Sales()
      ->where('estado', '1')
      ->where(function ($query) {
        $query->where('estado_pago', 'PENDIENTE')
          ->orWhere('estado_pago', 'PARCIAL');
      })
      ->sum('saldo_pendiente');
  }
}