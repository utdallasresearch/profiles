<?php

namespace App\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Tags\Tag;

class TagsTable extends Component
{
    use AuthorizesRequests, WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public $tag_type_filter = '';

    public $per_page = 10;

    public $sort_field = 'id';

    public $sort_descending = true;

    public function sortBy($field)
    {
        $this->sort_descending = ($this->sort_field === $field) ? !$this->sort_descending : false;
        $this->sort_field = $field;
    }

    public function updating($name)
    {
        // reset pagination when searching or filtering
        if (in_array($name, ['search', 'tag_type_filter', 'per_page'])) {
            $this->resetPage();
        }
    }

    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);

        $tag_name = $tag->name;

        $tag->delete();

        $this->emit('alert', "Deleted tag $tag_name", 'success');
    }

    public function render()
    {
        $tags_query = Tag::query()
            ->when($this->tag_type_filter, function($q) {
                $q->where('type', $this->tag_type_filter);
            })
            ->when($this->search, function($q) {
                $q->containing($this->search);
            })
            ->orderBy($this->sort_field, $this->sort_descending ? 'desc' : 'asc');

        return view('livewire.tags-table', [
            'tags' => $tags_query->paginate($this->per_page),
            'tag_types' => Tag::groupBy('type')->pluck('type'),
        ]);
    }
}
