<?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Radio Buttons']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Radio Buttons']); ?>
    <div class="flex flex-wrap items-center gap-8">
        <div x-data="{ checkboxToggle: false }">
            <label for="radioLabelOne"
                class="flex cursor-pointer items-center text-sm font-medium text-gray-700 select-none dark:text-gray-400">
                <div class="relative">
                    <input type="checkbox" id="radioLabelOne" class="sr-only" @change="checkboxToggle = !checkboxToggle" />
                    <div :class="checkboxToggle ? 'border-brand-500 bg-brand-500' :
                        'bg-transparent border-gray-300 dark:border-gray-700'"
                        class="hover:border-brand-500 dark:hover:border-brand-500 mr-3 flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                        <span class="h-2 w-2 rounded-full"
                            :class="checkboxToggle ? 'bg-white' : 'bg-white dark:bg-[#171f2e]'"></span>
                    </div>
                </div>
                Default
            </label>
        </div>

        <div x-data="{ checkboxToggle: true }">
            <label for="radioLabelTwo"
                class="flex cursor-pointer items-center text-sm font-medium text-gray-700 select-none dark:text-gray-400">
                <div class="relative">
                    <input type="checkbox" id="radioLabelTwo" class="sr-only"
                        @change="checkboxToggle = !checkboxToggle" />
                    <div :class="checkboxToggle ? 'border-brand-500 bg-brand-500' :
                        'bg-transparent border-gray-300 dark:border-gray-700'"
                        class="hover:border-brand-500 dark:hover:border-brand-500 mr-3 flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                        <span class="h-2 w-2 rounded-full"
                            :class="checkboxToggle ? 'bg-white' : 'bg-white dark:bg-[#171f2e]'"></span>
                    </div>
                </div>
                Secondary
            </label>
        </div>

        <div x-data="{ checkboxToggle: false }">
            <label for="radioLabelThree"
                class="flex cursor-pointer items-center text-sm font-medium text-gray-300 select-none dark:text-gray-700">
                <div class="relative">
                    <input type="checkbox" id="radioLabelThree" class="peer sr-only"
                        @change="checkboxToggle = !checkboxToggle" disabled />
                    <div :class="checkboxToggle ? 'bg-transparent border-gray-300 dark:border-gray-700' :
                        'border-brand-500 bg-brand-500'"
                        class="mr-3 flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                        <span class="h-2 w-2 rounded-full"
                            :class="checkboxToggle ? 'bg-white' : 'bg-white dark:bg-[#171f2e]'"></span>
                    </div>
                </div>
                Disabled Secondary
            </label>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb8dfe58016103e374219da4cf072c7cf)): ?>
<?php $attributes = $__attributesOriginalb8dfe58016103e374219da4cf072c7cf; ?>
<?php unset($__attributesOriginalb8dfe58016103e374219da4cf072c7cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb8dfe58016103e374219da4cf072c7cf)): ?>
<?php $component = $__componentOriginalb8dfe58016103e374219da4cf072c7cf; ?>
<?php unset($__componentOriginalb8dfe58016103e374219da4cf072c7cf); ?>
<?php endif; ?>
<?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/components/form/form-elements/radio-buttons.blade.php ENDPATH**/ ?>