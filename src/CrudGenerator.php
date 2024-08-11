<?php

namespace Kaysr\CrudMaker;

use Illuminate\Console\Command;

class CrudGenerator extends Command
{
    protected $signature = 'make:crud {name : Class (singular) for example User}';
    protected $description = 'Create CRUD operations';

    public function __construct()
    {
        parent::__construct();
    }

    private string $tableNameIsDefaultOption = "Leave default";

    private string $tableNameIsManuallyOption = "Name manually";

    private string $tableName;

    private string $modelName;

    public function handle()
    {
        $name = $this->argument('name');

        $this->modelName = \str($name)->singular()->ucfirst()->toString();

        $this->collectData();

        $this->model();

        $this->migration();

        $requestNames = $this->request();

        $this->service();

        $this->controller($requestNames);

        $this->info('CRUD for ' . $name . ' created successfully.');
    }

    protected function collectData()
    {

        $tableNamingOption = $this->choice("Leave table name as default or name yourself ? ", [
            $this->tableNameIsDefaultOption,
            $this->tableNameIsManuallyOption,
        ]);

        if ($tableNamingOption === $this->tableNameIsManuallyOption) {
            $this->tableName = $this->ask("What should be table named ?");
            return;
        }

        $this->tableName = str($this->modelName)->plural()->snake()->lower()->toString();

    }

    protected function getStub($type)
    {
        return file_get_contents(__DIR__."/../stubs/$type.stub");
    }

    protected function model()
    {
        $modelTemplate = str_replace(
            ['{{modelName}}', '{{tableName}}'],
            [$this->modelName, $this->tableName],
            $this->getStub('Model')
        );


        file_put_contents(app_path("Models/{$this->modelName}.php"), $modelTemplate);
    }

    protected function controller($requestNames)
    {
        [$storeModelRequestName, $updateRequestModelName] = $requestNames;

        $controllerResponses = [
            "view" => [
                "method" => [
                    "index"  => 'public function index()
                     {
                         ${{modelNamePluralCamelCase}} = $this->{{serviceName}}->getAllPaginated();
                         return {{response.index}};
                     }',
                    "create" =>'public function create()
                     {
                         return {{response.create}};
                     }',
                    "store" =>'public function store({{StoreModelRequest}} $request)
                     {
                         $this->{{serviceName}}->create($request->validated());
                         return {{response.store}};
                     }',
                    "show" =>'public function show($id)
                    {
                       ${{modelNameSingularCamelCase}} = $this->{{serviceName}}->findById($id);
                       return {{response.show}};
                    }',
                    "edit" =>'public function edit($id)
                    {
                       ${{modelNameSingularCamelCase}} = $this->{{serviceName}}->findById($id);
                       return {{response.edit}};
                    }',
                    "update" =>'public function update({{UpdateModelRequest}} $request, $id)
                    {
                        $this->{{serviceName}}->update($id, $request->validated());
                        return {{response.update}};
                    }',
                    "destroy" =>'public function destroy($id)
                    {
                        $this->{{serviceName}}->delete($id);
                        return  {{response.destroy}};
                    }'
                ],
                "response" => [
                    "index" => "view('{{modelNameSingularSnakeCase}}.index', compact('{{modelNamePluralCamelCase}}'))",
                    "create" => "view('{{modelNameSingularSnakeCase}}.create')",
                    "store" => "redirect()->route('{{modelNameSingularSnakeCase}}.index')->with('success', '{{modelName}} created successfully.')",
                    "show" => "view('{{modelNameSingularSnakeCase}}.show', compact('{{modelNameSingularCamelCase}}'))",
                    "edit" => "view('{{modelNameSingularSnakeCase}}.edit', compact('{{modelNameSingularCamelCase}}'))",
                    "update" => "redirect()->route('{{modelNameSingularSnakeCase}}.index')->with('success', '{{modelName}} updated successfully.')",
                    "destroy" => "redirect()->route('{{modelNameSingularSnakeCase}}.index')->with('success', '{{modelName}} deleted successfully.')",
                ]
            ],
            "inertia" => [
                "method"=>[
                    "index"  => 'public function index()
                     {
                         ${{modelNamePluralCamelCase}} = $this->{{serviceName}}->getAllPaginated();
                         return {{response.index}};
                     }',
                    "create" =>'public function create()
                     {
                         return {{response.create}};
                     }',
                    "store" =>'public function store({{StoreModelRequest}} $request)
                     {
                         $this->{{serviceName}}->create($request->validated());
                         return {{response.store}};
                     }',
                    "show" =>'public function show($id)
                     {
                        ${{modelNameSingularCamelCase}} = $this->{{serviceName}}->findById($id);
                        return {{response.show}};
                     }',
                    "edit" =>'public function edit($id)
                     {
                        ${{modelNameSingularCamelCase}} = $this->{{serviceName}}->findById($id);
                        return {{response.edit}};
                     }',
                    "update" =>'public function update({{UpdateModelRequest}} $request, $id)
                     {
                         $this->{{serviceName}}->update($id, $request->validated());
                         return {{response.update}};
                     }',
                    "destroy" =>'public function destroy($id)
                     {
                         $this->{{serviceName}}->delete($id);
                         return  {{response.destroy}};
                     }'
                ],
                "response" => [
                    "index" => "inertia('{{modelNameSingularSnakeCase}}.index', compact('{{modelNamePluralCamelCase}}'))",
                    "create" => "inertia('{{modelNameSingularSnakeCase}}.create')",
                    "store" => "redirect()->route('{{modelNameSingularSnakeCase}}.index')->with('success', '{{modelName}} created successfully.')",
                    "show" => "inertia('{{modelNameSingularSnakeCase}}.show', compact('{{modelNameSingularCamelCase}}'))",
                    "edit" => "inertia('{{modelNameSingularSnakeCase}}.edit', compact('{{modelNameSingularCamelCase}}'))",
                    "update" => "redirect()->route('{{modelNameSingularSnakeCase}}.index')->with('success', '{{modelName}} updated successfully.')",
                    "destroy" => "redirect()->route('{{modelNameSingularSnakeCase}}.index')->with('success', '{{modelName}} deleted successfully.')",
                ]
            ],
            "json" => [
                "method"=>[
                    "index"  => 'public function index()
                                  {
                                      ${{modelNamePluralCamelCase}} = $this->{{serviceName}}->getAllPaginated();
                                      return {{response.index}};
                                  }',
                    "create"=>"",
                    "store" =>'public function store({{StoreModelRequest}} $request)
                    {
                        $this->{{serviceName}}->create($request->validated());
                        return {{response.store}};
                    }',
                    "show" =>'public function show($id)
                    {
                       ${{modelNameSingularCamelCase}} = $this->{{serviceName}}->findById($id);
                       return {{response.show}};
                    }',
                    "edit"=>"",
                    "update" =>'public function update({{UpdateModelRequest}} $request, $id)
                     {
                         $this->{{serviceName}}->update($id, $request->validated());
                         return {{response.update}};
                     }',
                    "destroy" =>'public function destroy($id)
                    {
                        $this->{{serviceName}}->delete($id);
                        return  {{response.destroy}};
                    }'
                ],
                "response" => [
                    "index" => "response()->json(compact('{{modelNamePluralCamelCase}}'))",
                    "show" => "response()->json(compact('{{modelNameSingularCamelCase}}'))",
                    "store" => "response()->json(['success'=>true])",
                    "update" => "response()->json(['success'=>true])",
                    "destroy" => "response()->noContent()",
                ]
            ]
        ];


        $response = $this->choice(
            "What should be controller method responses based on ?",
            ["view", "json", "inertia"]
        );

        $template = $this->getStub('Controller');

        $templateDetails = $controllerResponses[$response];


        foreach ($templateDetails["method"] as $methodName => $method){
            $action = $method ? "replace" : "remove";

            $args = $method ? ["{{method.$methodName}}",$method] : ["{{method.$methodName}}"];

            $template = \str($template)->{$action}(...$args)->toString();
        }


        foreach ($templateDetails["response"] as $method => $methodResponse){
            $template = \str($template)->replace("{{response.$method}}",$methodResponse)->toString();
        }


        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{serviceName}}',
                '{{modelNameSingularSnakeCase}}',
                '{{modelNamePluralCamelCase}}',
                '{{modelNameSingularCamelCase}}',
                '{{StoreModelRequest}}',
                '{{UpdateModelRequest}}',
            ],
            [
                $this->modelName,
                \str("{$this->modelName}Service")->camel()->toString(),
                \str($this->modelName)->singular()->snake()->toString(),
                \str($this->modelName)->plural()->camel()->toString(),
                \str($this->modelName)->singular()->camel()->toString(),
                $storeModelRequestName,
                $updateRequestModelName
            ],
            $template
        );

        file_put_contents(app_path("Http/Controllers/{$this->modelName}Controller.php"), $controllerTemplate);
    }

    protected function request()
    {
        $requestTemplate = str_replace(
            ['{{modelName}}'],
            [$this->modelName],
            $this->getStub('Request')
        );

        $modelNameInRequest = \str($this->modelName)->singular()->ucfirst()->toString();

        if (!file_exists($path = app_path("/Http/Requests/$modelNameInRequest")))
            mkdir($path, 0777, true);


        $storeModelRequestName = "Store{$modelNameInRequest}Request";

        file_put_contents(
            app_path("Http/Requests/$modelNameInRequest/$storeModelRequestName.php"),
            \str($requestTemplate)
                ->replace("{{requestName}}", $storeModelRequestName)
                ->replace("{{modelName}}", $modelNameInRequest)
                ->toString()
        );

        $updateModelRequestName = "Update{$modelNameInRequest}Request";

        file_put_contents(
            app_path("Http/Requests/$modelNameInRequest/$updateModelRequestName.php"),
            \str($requestTemplate)
                ->replace("{{requestName}}", $updateModelRequestName)
                ->replace("{{modelName}}", $modelNameInRequest)
                ->toString()
        );

        return [$storeModelRequestName, $updateModelRequestName];
    }

    protected function migration()
    {
        $migrationTemplate = str_replace(
            ['{{tableName}}'],
            [$this->tableName],
            $this->getStub('Migration')
        );

        $migrationFileName = date('Y_m_d_His_') . "create_" . $this->tableName . "_table.php";

        file_put_contents(database_path("migrations/{$migrationFileName}"), $migrationTemplate);
    }

    protected function service()
    {
        $serviceTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}',
            ],
            [
                $this->modelName,
                str($this->modelName)->singular()->camel()->toString()
            ],
            $this->getStub('Service')
        );

        if (!file_exists($path = app_path('/Services')))
            mkdir($path, 0777, true);

        file_put_contents(app_path("Services/{$this->modelName}Service.php"), $serviceTemplate);
    }
}
