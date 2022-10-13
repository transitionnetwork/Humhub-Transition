TODOS 
=====

Create project on Gitlab and make it private.

Clone it and copy the files of this module model in it.

Replace theses keywords in all files :
- `module-model` (module id & HTML tags (class, id))
- `humhub\modules\transition` (namespace)
- `* Module Model` (files third line)
- `My module model` (displayed module name)
- `ModuleModelModule` (translations)
- `module_model` (SQL table)
- `'eye'` (icon in Module.php)

Rename files in `resources` subfolders

Replace module images in `resources` : to create icon, download an SVG and edit it with Inkscape :
- add a layer and put it in the background
- create a polygon with 4 corners and round value to 0,150
- export to PNG 300x300 px

Enable module in /admin/module/list

Create messages : `sudo -u www-data php yii message/extract-module module-model`

Create migrations : `php yii migrate/create initial --migrationPath='@app/modules/module-model/migrations'`, in safeUp():
```
        $this->createTable('module_model', [
            'id' => $this->primaryKey(),
            'object_model' => $this->string(100)->notNull(),
            'object_id' => $this->integer(11)->notNull(),
            'note' => $this->text(),
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(11),
            'updated_at' => $this->dateTime(),
            'updated_by' => $this->integer(11),
        ], '');
        
        // Add indexes on columns for speeding where operations ; false if values (or values combinaisons if several columns) are not unique 
        $this->createIndex('idx-module_model', 'module_model', ['user_id'], true);
        // Add foreign keys (if related to a table, when deleted in this table, related rows are deleted to, but beforeDelete() and afterDelete() are not called)
        $this->addForeignKey('fk-module_model-user', 'module_model', 'user_id', 'user', 'id', 'CASCADE');
```

Auto translate in all languages

Add screenshots (1024x545) in `resources` 

Update `module.json`

Make the source code public on https://gitlab.com/cuzy/humhub-modules-module_id

See `CUZY.APP/TODO when adding a new module.md`