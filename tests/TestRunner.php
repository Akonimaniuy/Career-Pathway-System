<?php
class TestRunner
{
    protected $failures = 0;
    protected $success = 0;

    public function assert($cond, $msg = '')
    {
        if ($cond) {
            $this->success++;
            echo "[PASS] $msg\n";
        } else {
            $this->failures++;
            echo "[FAIL] $msg\n";
        }
    }

    public function summary()
    {
        echo "\nSummary: {$this->success} passed, {$this->failures} failed\n";
        return $this->failures === 0;
    }
}