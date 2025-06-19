<?php

namespace admin\pages\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use admin\pages\Requests\PageCreateRequest;
use admin\pages\Requests\PageUpdateRequest;
use admin\pages\Models\Page;

class PageManagerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $pages = Page::
                filter($request->query('keyword'))
                ->filterByStatus($request->query('status'))
                ->orderBy('created_at', 'desc')
                ->paginate(5)
                ->withQueryString();

            return view('page::admin.index', compact('pages'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load pages: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('page::admin.createOrEdit');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load pages: ' . $e->getMessage());
        }
    }

    public function store(PageCreateRequest $request)
    {
        try {
            $requestData = $request->validated();

            Page::create($requestData);
            return redirect()->route('admin.pages.index')->with('success', 'Page created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load pages: ' . $e->getMessage());
        }
    }

    /**
     * show page details
     */
    public function show(Page $page)
    {
        try {
            return view('page::admin.show', compact('page'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load pages: ' . $e->getMessage());
        }
    }

    public function edit(Page $page)
    {
        try {
            return view('page::admin.createOrEdit', compact('page'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load page for editing: ' . $e->getMessage());
        }
    }

    public function update(PageUpdateRequest $request, Page $page)
    {
        try {
            $requestData = $request->validated();

            $page->update($requestData);
            return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load page for editing: ' . $e->getMessage());
        }
    }

    public function destroy(Page $page)
    {
        try {
            $page->delete();
            return response()->json(['success' => true, 'message' => 'Record deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete record.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $page = Page::findOrFail($request->id);
            $page->status = $request->status;
            $page->save();

            // create status html dynamically        
            if ($page->status == 'published') {
                $strMessage = 'Page status updated successfully to published.';
                $strHtml = '<a href="javascript:void(0)"'
                    . ' data-toggle="tooltip"'
                    . ' data-placement="top"'
                    . ' title="Click to change status to draft"'
                    . ' data-url="' . route('admin.pages.updateStatus') . '"'
                    . ' data-method="POST"'
                    . ' data-status="draft"'
                    . ' data-id="' . $page->id . '"'
                    . ' class="btn btn-success btn-sm update-status">Published</a>';
            } else {
                $strMessage = 'Page status updated successfully to draft.';
                $strHtml = '<a href="javascript:void(0)"'
                    . ' data-toggle="tooltip"'
                    . ' data-placement="top"'
                    . ' title="Click to change status to published"'
                    . ' data-url="' . route('admin.pages.updateStatus') . '"'
                    . ' data-method="POST"'
                    . ' data-status="published"'
                    . ' data-id="' . $page->id . '"'
                    . ' class="btn btn-warning btn-sm update-status">Draft</a>';
            }

            return response()->json(['success' => true, 'message' => $strMessage, 'strHtml' => $strHtml]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete record.', 'error' => $e->getMessage()], 500);
        }
    }
}
