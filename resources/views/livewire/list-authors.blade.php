<div>
    <div class="d-flex flex-row flex-wrap">
        <a data-toggle="modal" data-target="#addmodal" id="addmodalbtn" class="btn btn-outline-primary m-3 ">إضافة
            جديد</a>

    </div>

    <input wire:model.live="search_text" placeholder="بحث عن كاتب" class="form-control my-2">
    <table class="table">
        <thead>
            <tr>
                <th scope="col">صورة الكاتب</th>
                <th scope="col">اسم الكاتب <span wire:click="toggleSortDirection('name')"class="fa fa-angle-up">
                    </span></th>
                <th scope="col">عمل الكاتب <span wire:click="toggleSortDirection('work')"class="fa fa-angle-up">
                    </span></th>
                <th scope="col">نبذة عن الكاتب</th>
                <th>المنصات</th>
                <th> العمليات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($authors as $item)
                <tr>
                    <td><img src="{{ $item->image ? asset('storage/' . $item->image) : asset('images/placeholder.png') }}"
                            width="80px" alt=""> </td>
                    <th>{{ $item->name }}</th>
                    <td>{{ $item->work }}</td>
                    <td>{{ $item->summary }}</td>
                    <td>
                        @if ($item->platforms)
                            @foreach ($item->platforms as $item2)
                                {{ $item2->name }}
                            @endforeach
                        @endif
                    </td>
                    <td>

                        <a data-toggle="modal" data-target="#addmodal" wire:click="edit({{ $item->id }})"><span
                                class="fa fa-edit"></span> </a>
                        <a wire:click="destroy({{ $item->id }})"><span class="fa fa-remove "></span></a>
                    </td>
                </tr>
            @endforeach


        </tbody>
    </table>


    <div wire:ignore.self class="modal fade addmodal" id="addmodal" tabindex="-1" aria-labelledby="addmodalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="z-index: 1" role="dialog" aria-hidden="true"
            role="document">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title"> {{ $author ? 'تعديل الكاتب' : 'إضافة كاتب' }}</h5>

                </div>

                <form wire:submit.prevent="save">

                    <div class="row">
                        <label for="formFileMultiple"
                            class="btn btn-primary btn-sm form-label  mx-5 mt-3 p-2 w-100 radius-10 text-center"><span
                                class="fa fa-image mx-2"></span> اختر الصورة</label>
                        <input class="form-control" type="file" wire:model="image" id="formFileMultiple" hidden
                            wire:loading.attr="disabled" wire:target='image' />

                        <div wire:loading wire:target="image">تحميل</div>
                        <div class="progress">
                            <div class="progress-bar" style="width:0%">0%</div>
                        </div>
                        <div class="cart-img-container">
                            @if ($image)
                                <div class="deleter" wire:click="deleteImage()">
                                    <span class="fa fa-trash "></span>
                                </div>

                                <div class="col-12 mb-1">
                                    <img class="img-fluid " src="{{ $image->temporaryUrl() }}">
                                </div>
                            @else
                                @if ($image2)
                                    <div class="deleter" wire:click="deleteImage()">
                                        <span class="fa fa-trash "></span>
                                    </div>
                                    <div class="col-12 mb-1">
                                        <img class="img-fluid " src="{{ asset('storage/' . $image2) }}">
                                    </div>
                                @endif

                            @endif
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="name">الاسم</label>
                        <input type="text" wire:model="name" class="form-control" id="name">

                    </div>
                    @error('name')
                        <span class="error red-text">{{ $message }}</span>
                    @enderror

                    <div class="form-group">
                        <label for="work">العمل</label>
                        <input type="text" wire:model="work" class="form-control" id="work">

                    </div>
                    @error('work')
                        <span class="error red-text">{{ $message }}</span>
                    @enderror
                    <div class="form-group">
                        <label for="summary">نبذة عن الكاتب</label>
                        <textarea type="text" wire:model="summary" class="form-control" id="summary"></textarea>

                    </div>
                    @error('summary')
                        <span class="error red-text">{{ $message }}</span>
                    @enderror

                    <a wire:click="addRow" class="btn btn-outline-primary m-3">إضافة منصة</a>
                    @foreach ($rows as $index => $row)
                        <div class="row mb-2 align-items-end">

                            <div class="col-md-5">
                                <label for="name"> ({{ $index + 1 }} ) اسم المنصة </label>
                                <input type="text" class="form-control" wire:model="rows.{{ $index }}.name">
                                @error('rows.' . $index . '.name')
                                    <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="url"> ({{ $index + 1 }} ) رابط المنصة </label>
                                <input type="text" class="form-control" wire:model="rows.{{ $index }}.url">
                                @error('rows.' . $index . '.url')
                                    <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="image"> ({{ $index + 1 }} ) ايقونة المنصة </label>
                                <input type="text" class="form-control"
                                    wire:model="rows.{{ $index }}.image">
                                @error('rows.' . $index . '.image')
                                    <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm"
                                    wire:click="deleteRow({{ $index }})">إزالة</button>
                            </div>
                        </div>
                    @endforeach

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                            wire:target='save'> <span class="spinner spinner-grow " wire:loading
                                wire:target='save'></span> حفظ</button>
                        <button type="button" class="btn btn-secondary" wire:click="closeModal()">إغلاق</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
