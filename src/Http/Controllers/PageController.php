<?php

namespace Agenciafmd\Pages\Http\Controllers;

use Agenciafmd\Pages\Models\Page;
use Agenciafmd\Pages\Http\Requests\PageRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PageController extends Controller
{
    public function index(Request $request)
    {
        session()->put('backUrl', request()->fullUrl());

        $query = QueryBuilder::for(Page::class)
            ->defaultSorts(config('admix-pages.default_sort'))
            ->allowedSorts($request->sort)
            ->allowedFilters(array_merge((($request->filter) ? array_keys(array_diff_key($request->filter, array_flip(['id', 'is_active']))) : []), [
                AllowedFilter::exact('id'),
                AllowedFilter::exact('is_active'),
            ]));

        if ($request->is('*/trash')) {
            $query->onlyTrashed();
        }

        $view['items'] = $query->paginate($request->get('per_page', 50));

        return view('agenciafmd/pages::index', $view);
    }

    public function create(Page $page)
    {
        $view['model'] = $page;

        return view('agenciafmd/pages::form', $view);
    }

    public function store(PageRequest $request)
    {
        if ($page = Page::create($request->validated())) {
            flash('Item inserido com sucesso.', 'success');
        } else {
            flash('Falha no cadastro.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route('admix.pages.index');
    }

    public function show(Page $page)
    {
        $view['model'] = $page;

        return view('agenciafmd/pages::form', $view);
    }

    public function edit(Page $page)
    {
        $view['model'] = $page;

        return view('agenciafmd/pages::form', $view);
    }

    public function update(Page $page, PageRequest $request)
    {
        if ($page->update($request->validated())) {
            flash('Item atualizado com sucesso.', 'success');
        } else {
            flash('Falha na atualização.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route('admix.pages.index');
    }

    public function destroy(Page $page)
    {
        if ($page->delete()) {
            flash('Item removido com sucesso.', 'success');
        } else {
            flash('Falha na remoção.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route('admix.pages.index');
    }

    public function restore($id)
    {
        $page = Page::onlyTrashed()
            ->find($id);

        if (!$page) {
            flash('Item já restaurado.', 'danger');
        } elseif ($page->restore()) {
            flash('Item restaurado com sucesso.', 'success');
        } else {
            flash('Falha na restauração.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route('admix.pages.index');
    }

    public function batchDestroy(Request $request)
    {
        if (Page::destroy($request->get('id', []))) {
            flash('Item removido com sucesso.', 'success');
        } else {
            flash('Falha na remoção.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route('admix.pages.index');
    }

    public function batchRestore(Request $request)
    {
        $page = Page::onlyTrashed()
            ->whereIn('id', $request->get('id', []))
            ->restore();

        if ($page) {
            flash('Item restaurado com sucesso.', 'success');
        } else {
            flash('Falha na restauração.', 'danger');
        }

        return ($url = session()->get('backUrl')) ? redirect($url) : redirect()->route('admix.pages.index');
    }
}
