<?php

namespace Froala\NovaFroalaField\Http\Controllers;

use Froala\NovaFroalaField\Froala;
use Froala\NovaFroalaField\Handlers\DiscardPendingAttachments;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ResourceDetailRequest;

class FroalaUploadController extends Controller
{
    /**
     * Store an attachment for a Trix field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(NovaRequest $request)
    {
        $field = $request->newResource()
                         ->availableFields($request)
                         ->findFieldByAttribute($request->field, function () {
                             abort(404);
                         });
        
        return response()->json([
            'link' => call_user_func(
                $field->attachCallback,
                $request
            ),
        ]);
    }
    
    /**
     * Delete a single, persisted attachment for a Trix field by URL.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyAttachment(NovaRequest $request)
    {
        $field = $request->newResource()
                         ->availableFields($request)
                         ->findFieldByAttribute($request->field, function () {
                             abort(404);
                         });
        
        return call_user_func($field->detachCallback, $request);
    }
    
    /**
     * Purge all pending attachments for a Trix field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyPending(NovaRequest $request)
    {
        $request = ResourceDetailRequest::createFrom($request);
        
        /** @var Froala $field */
        $field = $request->newResource()
                         ->availableFields($request)
                         ->findFieldByAttribute($request->field, function () {
                             abort(404);
                         });
        $field->discard(new DiscardPendingAttachments);
        return call_user_func($field->discardCallback, $request);
    }
}
