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
    /*protected function setFilamentTableToAll(Browser $browser)
    {
        $selector = 'select[wire:model="tableRecordsPerPage"]';
        if ($browser->element($selector)) {
            $browser->select($selector, '-1')
                    ->pause(2000);
        }
    }*/

    protected function navigateAllPages(Browser $browser, callable $callback)
    {
        $currentPage = 1;

        while (true) {
            // Thực hiện hành động (như kiểm tra text hoặc tích chọn checkbox)
            $callback($browser, $currentPage);

            // 1. Cuộn xuống cuối trang để đảm bảo các nút phân trang hiển thị
            $browser->script('window.scrollTo(0, document.body.scrollHeight);');
            $browser->pause(1000);

            // 2. Tìm nút "Tiếp" (Next) bằng CSS Selector dựa trên mã HTML
            $nextButtonSelector = 'button[rel="next"]';
            $nextButton = $browser->element($nextButtonSelector);

            // 3. Kiểm tra nếu nút tồn tại và bấm được (không bị disabled)
            if ($nextButton && $nextButton->isEnabled() && $nextButton->getAttribute('aria-disabled') !== 'true') {
                // Lưu text của dòng đầu để kiểm tra việc chuyển trang đã xong chưa
                $oldFirstRow = $browser->text('table tbody tr:first-child');

                $browser->click($nextButtonSelector);

                // 4. Đợi Livewire tải xong trang mới (nội dung bảng thay đổi)
                $browser->waitUntilJavaScript(
                    "document.querySelector('table tbody tr:first-child').innerText !== " . json_encode($oldFirstRow),
                    15
                )->pause(2000);

                $currentPage++;
            } else {
                // Không còn trang tiếp theo, thoát vòng lặp
                break;
            }
        }
    }
    //Chuyển đến một trang cụ thể trong bảng Filament (1, 2, 3...)
    protected function goToFilamentPage(Browser $browser, $pageNumber)
    {
        // 1. Cuộn xuống cuối để thấy thanh phân trang
        $browser->script('window.scrollTo(0, document.body.scrollHeight);');
        $browser->pause(1000);

        // 2. Tìm nút có aria-label chứa số trang tương ứng
        $selector = "button[aria-label*='Đi tới trang $pageNumber']";

        $browser->waitFor($selector, 10)
                ->click($selector)
                ->pause(3000); // Chờ Livewire tải trang mới
    }

    //Hàm bổ trợ: Xóa giá trị của các trường input thông thường (text, datetime, number)
    //Sử dụng Ctrl+A và Backspace để đảm bảo xóa sạch dữ liệu cũ.
    protected function clearFilamentInput(Browser $browser, $fieldId)
    {
        $selector = "input[id='{$fieldId}']";
        $browser->waitFor($selector, 10)
                ->keys($selector, ['{control}', 'a'], '{backspace}')
                ->pause(500);
    }

}
