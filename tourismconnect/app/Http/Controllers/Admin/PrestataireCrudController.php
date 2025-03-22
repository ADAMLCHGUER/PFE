<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class PrestataireCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Prestataire::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/prestataire');
        CRUD::setEntityNameStrings('prestataire', 'prestataires');
    }

    protected function setupListOperation()
    {
        CRUD::column('nom');
        CRUD::column('email');
        CRUD::column('telephone');
        CRUD::column('ville');
        CRUD::column('pays');
        CRUD::column('actif')->type('boolean');
        
        // Utilisez une configuration plus détaillée pour la colonne image
        CRUD::addColumn([
            'name' => 'image',
            'type' => 'image',
            'disk' => 'public',
            'height' => '60px',
            'width' => '60px',
        ]);
        
        CRUD::column('created_at');
    }

    protected function setupCreateOperation()
{
    CRUD::setValidation([
        'nom' => 'required|min:2|max:255',
        'email' => 'required|email|unique:prestataires,email,' . request()->id,
        'telephone' => 'nullable|string|max:20',
        'description' => 'nullable|string',
        'adresse' => 'nullable|string|max:255',
        'ville' => 'nullable|string|max:100',
        'pays' => 'nullable|string|max:100',
        'code_postal' => 'nullable|string|max:20',
        'site_web' => 'nullable|url|max:255',
        'actif' => 'boolean',
        'image' => 'nullable|file|image|max:2048',
    ]);

    CRUD::field('nom');
    CRUD::field('email')->type('email');
    CRUD::field('telephone');
    CRUD::field('description')->type('textarea');
    CRUD::field('adresse');
    CRUD::field('ville');
    CRUD::field('pays');
    CRUD::field('code_postal');
    CRUD::field('site_web')->type('url');
    CRUD::field('actif')->type('checkbox');
    
    // Utilisez une configuration plus simple pour le champ image
    CRUD::addField([
        'name' => 'image',
        'type' => 'upload',
        'label' => 'Image',
        'upload' => true,
        'disk' => 'public',
        'path' => 'prestataires',
    ]);
}

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
    
    protected function setupShowOperation()
    {
        $this->setupListOperation();
        
        CRUD::column('description');
        CRUD::column('adresse');
        CRUD::column('code_postal');
        CRUD::column('site_web')->type('link');
        CRUD::column('updated_at');
    }
}