<?php

namespace Tests\Browser\Traits;

use Laravel\Dusk\Browser;

trait InteractsWithFilament
{
    /**
     * Hỗ trợ chọn Select của Filament (Choices.js) cho cả Thêm và Sửa
     */
    protected function selectFilamentChoice(Browser $browser, $fieldId, $searchKeyword, $exactMatch)
    {
        // 1. Mở dropdown
        $browser->script("
            let select = document.getElementById('$fieldId');
            if (select) {
                let container = select.closest('.choices');
                container.dispatchEvent(new MouseEvent('mousedown', {bubbles: true}));
                container.click();
            }
        ");

        $browser->pause(1000);

        // 2. Gõ từ khóa tìm kiếm
        $browser->script("
            let select = document.getElementById('$fieldId');
            let container = select.closest('.choices');
            let input = container.querySelector('.choices__input--cloned');
            if (input) {
                input.value = '$searchKeyword';
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new KeyboardEvent('keyup', { bubbles: true }));
            }
        ");

        $browser->pause(2000);

        // 3. Click vào item khớp với tên
        $browser->script("
            let container = document.getElementById('$fieldId').closest('.choices');
            let items = container.querySelectorAll('.choices__item--selectable');
            for (let item of items) {
                if (item.innerText.toLowerCase().includes('$exactMatch'.toLowerCase())) {
                    item.dispatchEvent(new MouseEvent('mousedown', {bubbles: true}));
                    item.click();
                    break;
                }
            }
        ");

        $browser->pause(500);
    }
  protected function setFilamentDateTime(Browser $browser, $fieldId, $dateTimeString)
    {
        // Dấu \$ giúp VS Code không báo lỗi biến PHP Undefined
        $browser->script("
            (function() {
                let el = document.getElementById('$fieldId');
                if (!el) return;

                // 1. Gán giá trị vào input
                el.value = '$dateTimeString';

                // 2. Ép Alpine.js cập nhật state để Livewire nhận dữ liệu
                if (window.Alpine) {
                    let alpineData = window.Alpine.\$data(el);
                    if (alpineData) {
                        alpineData.state = '$dateTimeString';
                    }
                } else if (el.__x) {
                    el.__x.\$data.state = '$dateTimeString';
                }

                // 3. Kích hoạt sự kiện đồng bộ
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
                el.blur();
            })();
        ");
    }
    protected function setFilamentTableToAll(Browser $browser)
    {
        $selector = 'select[wire:model="tableRecordsPerPage"]';
        if ($browser->element($selector)) {
            $browser->select($selector, '-1')
                    ->pause(2000);
        }
    }
}
