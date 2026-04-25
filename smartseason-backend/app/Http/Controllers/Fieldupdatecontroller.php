<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Fieldupdate;
use App\Http\Requests\StoreFieldUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Fieldupdatecontroller extends Controller
{
    //get/api/fields/{field}/updates.requests the full update history for a field.
    //admin see all fields,agents see history for theit assigned field
    public function index(Request $request,Field $field):JsonResponse
    {
        if($request=User()->isAgent())
            {
                $isAssigned = $field->assignments()
                            ->where('agent_id',$request->user()->id)
                            ->exists();
                          
               abort_if(!$isAssigned,403,'You are not assigned to this field.');             
            }

            $updates =$field->updates()
                    ->with('agent')
                    ->latest()
                    ->get();

              return response()->json($updates);      
    }
    //post/api/fields/{fields}/update.agents(or admin)post a field update and optional notes.
    //also syncs the fields current stage to the new stage
    public function store(StoreFieldUpdateRequest $request,Field $field):JsonResponse
    {
        $User = $request->User();
        if($User->isAgent())
            {
                $isAssigned =$field->assignments()
                            ->where ('agent_id',$User->id)
                            ->exists();
                
                \abort_if(!$isAssigned,403,'You are not assigned to this field.');            
            }
            //record the update in history
            $updates =Fieldupdate::create([
                'field_id'=>$field->id,
                'agent_id'=>$User->id,
                'new_stage'=>$request->new_stage,
                'notes'=>$request->notes,
            ]);
            //sync the fields stage to the latest updates
            $field->update(['stage'=>$request->new_stage]);
            return response()->json($updates->load('agent'),201);
    }
}
