<?php

namespace App\Listeners;

use App\Events\ResumeUploadEvent;
use App\Models\Profile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Smalot\PdfParser\Parser;

class ProcessUploadedResume implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(public readonly Parser $pdfParser)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(ResumeUploadEvent $event): void
    {
        $profile = $event->profile;

        $resumeFile = $profile->cv_path;

        $pdf = $this->pdfParser->parseContent(file_get_contents($resumeFile));

        $text = $pdf->getText();

        $skillsStart = strpos($text, 'SKILLS');
        $experienceStart = strpos($text, 'EXPERIENCE');
        $educationStart = strpos($text, 'EDUCATION');

        if ($skillsStart !== false && $experienceStart !== false && $educationStart !== false) {
            $skills = $this->getSkills($text);
            $experience = $this->getExperience($text);
            $education = $this->getEducation($text);
        }

        Profile::query()
            ->where('id', $profile->id)
            ->update([
                'work_experience' => $experience,
                'skills' => $skills,
                'education' => $education,
            ]);
    }

    private function getSkills($text)
    {
        $skillsStart = strpos($text, 'SKILLS');
        $experienceStart = strpos($text, 'EXPERIENCE');

        $cleanedSkills = [];
        $skillsSection = substr($text, $skillsStart, $experienceStart - $skillsStart);

        $skillsArray = preg_split('/\\t*\\n/', $skillsSection);

        $skillsArray = array_filter($skillsArray, function ($line) {
            return stripos($line, 'SKILLS') === false;
        });

        $cleanedSkills = array_merge($cleanedSkills, array_map('trim', array_filter($skillsArray)));

        $individualSkills = [];
        foreach ($cleanedSkills as $line) {
            $individualSkills[] = $line;
        }

        return $individualSkills;
    }

    private function getExperience($text)
    {
        $experienceStart = strpos($text, 'EXPERIENCE');

        $experienceSection = substr($text, $experienceStart);

        $experienceArray = preg_split('/\\t*\\n/', $experienceSection);

        $cleanedExperience = [];
        $experienceArray = array_filter($experienceArray, function ($line) {
            return stripos($line, 'EXPERIENCE') === false;
        });

        $cleanedExperience = array_merge($cleanedExperience, array_map('trim', array_filter($experienceArray)));

        $individualExperiences = [];
        foreach ($cleanedExperience as $line) {
            $individualExperiences[] = $line;
        }

        return $individualExperiences;
    }

    private function getEducation($text)
    {
        $educationStart = strpos($text, 'EDUCATION');

        $educationSection = substr($text, $educationStart);

        $educationArray = preg_split('/\\t*\\n/', $educationSection);

        $cleanedEducation = [];
        $educationArray = array_filter($educationArray, function ($line) {
            return stripos($line, 'EDUCATION') === false;
        });

        $cleanedEducation = array_merge($cleanedEducation, array_map('trim', array_filter($educationArray)));

        foreach ($educationArray as $line) {
            if (strpos($line, '•') === false) {
                break;
            }

            $cleanedEducation[] = $line;
        }

        $individualEducations = [];
        foreach ($cleanedEducation as $line) {
            $individualEducations[] = $line;
        }

        return $cleanedEducation;
    }
}
