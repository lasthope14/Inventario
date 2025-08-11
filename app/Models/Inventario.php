<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Storage;

class Inventario extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'categoria_id', 'proveedor_id', 'nombre', 'propietario',
        'modelo', 'numero_serie', 'marca', 'fecha_compra', 'numero_factura',
        'valor_unitario', 'fecha_baja', 'fecha_inspeccion', 'observaciones',
        'imagen_principal', 'imagen_secundaria', 'metadatos', 'codigo_unico', 'ubicacion_id'
    ];

    protected $dates = ['fecha_compra', 'fecha_baja', 'fecha_inspeccion'];

    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_baja' => 'date',
        'fecha_inspeccion' => 'date',
        'metadatos' => AsCollection::class,
        'valor_unitario' => 'float',
    ];

    protected $appends = ['valor_total'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('imagenes')
             ->useDisk('public')
             ->acceptsMimeTypes(['image/jpeg', 'image/png', 'gif']);

        $this->addMediaCollection('documentos')
             ->useDisk('public')
             ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'image/jpeg',
                'image/png',
                'text/plain',
                'application/zip',
                'application/x-rar-compressed',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation'
             ]);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->width(200)
             ->height(200)
             ->performOnCollections('imagenes')
             ->nonQueued();
    }

    // Métodos para manejo de medios
    public function guardarImagenes($imagen1 = null, $imagen2 = null)
    {
        if ($imagen1) {
            $fileName = $this->codigo_unico . '_1.' . $imagen1->getClientOriginalExtension();
            $destinationPath = 'inventario_imagenes/' . $fileName;
            
            $this->addMedia($imagen1)
                 ->preservingOriginal()
                 ->usingName($this->codigo_unico . '_1')
                 ->usingFileName($destinationPath)
                 ->toMediaCollection('imagenes', 'public');
        }
        if ($imagen2) {
            $fileName = $this->codigo_unico . '_2.' . $imagen2->getClientOriginalExtension();
            $destinationPath = 'inventario_imagenes/' . $fileName;
            
            $this->addMedia($imagen2)
                 ->preservingOriginal()
                 ->usingName($this->codigo_unico . '_2')
                 ->usingFileName($destinationPath)
                 ->toMediaCollection('imagenes', 'public');
        }
    }

    public function guardarDocumento($documento)
    {
        if ($documento) {
            $fileName = $this->codigo_unico . '_' . pathinfo($documento->getClientOriginalName(), PATHINFO_FILENAME) 
                     . '.' . $documento->getClientOriginalExtension();
            $destinationPath = 'documentos/' . $fileName;
            
            $this->addMedia($documento)
                 ->preservingOriginal()
                 ->usingName($this->codigo_unico . '_doc')
                 ->usingFileName($destinationPath)
                 ->toMediaCollection('documentos', 'public');
        }
    }

    // Relaciones
    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
    }

    // Relación many-to-many con ubicaciones a través de tabla pivot
    public function ubicaciones()
    {
        return $this->belongsToMany(Ubicacion::class, 'inventario_ubicaciones')
                    ->withPivot('cantidad', 'estado')
                    ->withTimestamps();
    }

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class);
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    public function elementos()
    {
        return $this->hasMany(ElementoInventario::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }

    public function checklists()
    {
        return $this->hasMany(Checklist::class);
    }

    // Attributes
    protected function cantidad(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ubicaciones()->sum('cantidad') ?: 1,
        );
    }

    protected function fechaInspeccion(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value) : null,
        );
    }

    public function getValorTotalAttribute()
    {
        return $this->valor_unitario * $this->getCantidadTotalAttribute();
    }

    public function getCantidadTotalAttribute()
    {
        return $this->ubicaciones->sum('cantidad');
    }

    // Formatted Dates
    public function getFechaCompraFormattedAttribute()
    {
        return $this->fecha_compra ? $this->fecha_compra->format('d/m/Y') : '';
    }

    public function getFechaBajaFormattedAttribute()
    {
        return $this->fecha_baja ? $this->fecha_baja->format('d/m/Y') : '';
    }

    public function getFechaInspeccionFormattedAttribute()
    {
        return $this->fecha_inspeccion ? $this->fecha_inspeccion->format('d/m/Y') : '';
    }

    // Media Methods
    public function getImagenPrincipal()
    {
        return $this->getMedia('imagenes')->first();
    }

    public function getImagenSecundaria()
    {
        return $this->getMedia('imagenes')->slice(1)->first();
    }

    public function getDocumentos()
    {
        return $this->getMedia('documentos');
    }

    // Scopes y métodos estáticos
    public function scopeCountByState($query)
    {
        return $query->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN estado = "disponible" THEN 1 ELSE 0 END) as disponibles,
            SUM(CASE WHEN estado = "en uso" THEN 1 ELSE 0 END) as en_uso,
            SUM(CASE WHEN estado = "en mantenimiento" THEN 1 ELSE 0 END) as en_mantenimiento,
            SUM(CASE WHEN estado = "dado de baja" THEN 1 ELSE 0 END) as dados_de_baja,
            SUM(CASE WHEN estado = "robado" THEN 1 ELSE 0 END) as robados
        ');
    }

    public static function getCountsByCategory($categoryId)
    {
        return static::where('categoria_id', $categoryId)
            ->countByState()
            ->first();
    }

    public static function generarCodigoUnico($categoria, $nombre)
    {
        $prefijo = $categoria->prefijo;
        
        $abreviatura = implode('', array_map(function($word) {
            return strtoupper(substr($word, 0, 1));
        }, explode(' ', $nombre)));
        
        $abreviatura = substr($abreviatura, 0, 3);
        
        $ultimoCodigo = self::where('codigo_unico', 'LIKE', "{$prefijo}-{$abreviatura}-%")
                            ->orderByRaw('CAST(SUBSTRING(codigo_unico, -3) AS UNSIGNED) DESC')
                            ->first();
        
        $numero = $ultimoCodigo ? 
            (intval(substr($ultimoCodigo->codigo_unico, -3)) + 1) : 
            1;
        
        return sprintf("%s-%s-%03d", $prefijo, $abreviatura, $numero);
    }

    public function actualizarCodigoUnico()
    {
        $categoria = $this->categoria;
        $nuevoCodigoUnico = self::generarCodigoUnico($categoria, $this->nombre);
        $this->update(['codigo_unico' => $nuevoCodigoUnico]);
        return $nuevoCodigoUnico;
    }
}