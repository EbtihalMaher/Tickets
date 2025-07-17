<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @bodyParam data.attributes.title string required The ticket's title. No-example
 * @bodyParam data.relationships.author.data.id integer required The author id. No-example
 */
class StoreTicketRequest extends BaseTicketRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isTicketController = $this->routeIs('tickets.store');
        $authorIdAttribute = $isTicketController ? 'data.relationships.author.data.id' : 'author';
        $user = $this->user();
        $authorRule = 'required|integer|exists:users,id';

        $rules = [
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'requ ired|string|in:A,C,H,X',
            
        ];

        if ($isTicketController) {
            $rules['data.relationships'] = 'required|array';
            $rules['data.relationships.author'] = 'required|array';
            $rules['data.relationships.author.data'] = 'required|array';
        }

        if ($user) {
            if ($user->tokenCan(Abilities::CreateTicket)) {
                $rules[$authorIdAttribute] = $authorRule;
            } else {
                $rules[$authorIdAttribute] = $authorRule . '|size:' . $user->id;
            }
        } else {
            $rules[$authorIdAttribute] = $authorRule;
        }
        
        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->routeIs('authors.tickets.store')) {
            $this->merge([
                'author' => $this->route('author')
            ]);
        }
    }

    public function bodyParameters() {
        $documentation = [
            'data.attributes.title' => [
                'description' => "The ticket's title (method)",
                'example' => "No-example"
            ],
            'data.attributes.description' => [
                'description' => "The ticket's description (method)",
                'example' => "No-example"
            ],
            'data.attributes.status' => [
                'description' => "The ticket's status (method)",
                'example' => "No-example"
            ],
        ];

        if ($this->routeIs('tickets.store')) {
            $documentation['data.relationships.author.data.id'] = [
                'description' => "The author assigned to the ticket.",
                'example' => "No-example"
            ];
        } else {
            $documentation['author'] = [
                'description' => "The author assigned to the ticket.",
                'example' => "No-example"
            ];
        }

        return $documentation;
    }
}
