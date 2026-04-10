<x-layouts.app title="Submissions">
    <div class="space-y-8">
        <div class="bg-white rounded-xl shadow-sm p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">My Submissions</h2>
            <p class="text-gray-600 text-lg">Submit your practicum assignments and track their status.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-6">Submit New Assignment</h3>
            <form class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Module</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option>Module 3: JavaScript</option>
                        <option>Module 4: PHP Basics</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assignment Type</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option>Code Submission</option>
                        <option>Report</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Upload</label>
                    <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Submit Assignment
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
