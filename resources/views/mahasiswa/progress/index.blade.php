<x-layouts.app title="Progress">
    <div class="space-y-8">
        <div class="bg-white rounded-xl shadow-sm p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">My Progress</h2>
            <p class="text-gray-600 text-lg">Track your grades and completion status across all modules.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl shadow-sm p-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-6">Module Grades</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">Module 1: Introduction</span>
                        <span class="text-green-600 font-bold">A (95)</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">Module 2: HTML & CSS</span>
                        <span class="text-green-600 font-bold">A- (90)</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">Module 3: JavaScript</span>
                        <span class="text-yellow-600 font-bold">B+ (85)</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-6">Overall Statistics</h3>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Average Grade</span>
                        <span class="font-bold text-gray-800">A- (90.0)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Modules Completed</span>
                        <span class="font-bold text-gray-800">3/12</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Assignments Submitted</span>
                        <span class="font-bold text-gray-800">6/8</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
