<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\FieldAssignment;
use App\Http\Requests\AssignFieldRequest;
use Illuminate\Http\JsonResponse;

class Fieldassignmentcontroller extends Controller
{
    //post/api/fields/{field}/assignments.admin assigns an agent to a field
    //-field-id comes from the url via route model binding{field}
    //-agent-id comes from the json response request body
    //uses firstOrcreate to prevent duplicate assignment silently
    public function store(AssignFieldRequest $request,Field $field):JsonResponse
    {
        $assignment = FieldAssignment::firstOrCreate([
            'field_id'=>$field->id,
            'agent_id'=>$request->agent_id
        ]);

        return response()->json($assignment->load('agent'),201);
    }
    //DELETE/api/fields/{field}/assignments/{{agentid}}
    //admin removes an agent from a field
    //-field-id comes from{field}in the url
    //-agentid comes from{agentid}from the json 

    public function destroy(Field $field,int $agentid):JsonResponse
    {
        $deleted = FieldAssignment::where('field_id', $field->id)
                                  ->where('agent_id',$agentid)
                                  ->delete();
        abort_if(!$deleted,404,'Assignment not found!');
        return response()->json(['message'=>'Agent unassigned successfully.']);                         
    }
}
