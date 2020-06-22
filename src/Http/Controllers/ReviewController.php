<?php

namespace App\Http\Controllers;

use App\Review;
use App\Specialist;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * Review Controller
 * 
 * @author Emma NWAMAIFE <emadimabua@gmail.com>
 */
class ReviewController extends Controller
{
    public function add(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->is_patient) {
            return response()->json([
                'status' => false, 'message' => 'Review could not be saved',
                'errors' => ['error' => 'You cannot give reviews as a non-patient']
            ], 403);
        }

        $data = $this->validate($request, [
            //'patient_id' => 'required|numeric|exists:App\Patient,user_id',
            'remark' => 'required|string|max:160',
            'rating' => 'required|numeric|between:0.5,5.0'
        ]);

        try {
            $specialist = Specialist::findOrFail($id);
            if (empty($specialist)) {
                throw new ModelNotFoundException("Specialist could not be found");
            }


            $review = new Review($data);
            $review->specialist_id = $id;
            $review->patient_id = $user->id;
            $review->save();

            return response()->json([
                'status' => true, 'message' => 'Review saved successfully', 'data' => $review
            ], 201);
        } catch (\Exception | ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 'message' => 'Review could not be saved', 'errors' => ['error' => $e->getMessage()]
            ], ($e instanceof ModelNotFoundException ? 404 : 400));
        }
    }

    public function edit(Request $request, $id, $reviewId)
    {
        $user = auth()->user();
        if (!$user->is_patient) {
            return response()->json([
                'status' => false, 'message' => 'Review could not be updated',
                'errors' => ['error' => 'You cannot edit reviews as a non-patient']
            ], 403);
        }

        $data = $this->validate($request, [
            'remark' => 'required|string|max:160', 'rating' => 'required|numeric|between:0.5,5.0'
        ]);

        try {
            $review = Review::with(['patient.user'])->where(['specialist_id' => $id])->findOrFail($reviewId);
            // if (empty($review)) {
            //     abort(404, 'Review does not exist');
            // }

            if ($review->patient->user->id !== $user->id) {
                abort(403, 'Review cannot be edited by proxy');
            }

            $review->remark = $data['remark'];
            $review->rating = $data['rating'];
            $review->save();

            return response()->json([
                'status' => true, 'message' => 'Review updated successfully', 'data' => $review
            ], 200);
        } catch (\Exception | ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 'message' => 'Review could not be updated', 'errors' => ['error' => $e->getMessage()]
            ], ($e instanceof ModelNotFoundException ? 404 : 400));
        }
    }

    public function delete($id, $reviewId)
    {
        $user = auth()->user();
        if (!$user->is_patient) {
            return response()->json([
                'status' => false, 'message' => 'Review could not be deleted',
                'errors' => ['error' => 'You cannot delete reviews as a non-patient']
            ], 403);
        }

        try {
            $review = Review::with(['patient.user'])->where(['specialist_id' => $id])->findOrFail($reviewId);
            if ($review->patient->user->id !== $user->id) {
                abort(403, 'Review cannot be deleted by proxy');
            }

            $review->delete();

            return response()->json([
                'status' => true, 'message' => 'Review deleted successfully', 'data' => $review
            ], 200);
        } catch (\Exception | ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 'message' => 'Review could not be deleted', 'errors' => ['error' => $e->getMessage()]
            ], ($e instanceof ModelNotFoundException ? 404 : 400));
        }
    }

    public function fetch($id = 0)
    {
        try {
            $user = auth()->user();
            $specialistId = ($user->is_specialist && !($user->is_patient || $user->is_admin)) ? $user->id : $id; // TODO: fix this issue

            $reviews = Review::where(['specialist_id' => $specialistId])->get();
            return response()->json(['status' => true, 'data' => $reviews]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false, 'message' => 'Review(s) could not be fetched',
                'errors' => ['error' => $e->getMessage()]
            ], 400);
        }
    }
}
