<?php

namespace PHPMD\Cache;

use PHPMD\Console\OutputInterface;
use PHPMD\RuleSet;
use PHPMD\TextUI\CommandLineOptions;

final class ResultCacheEngineFactory
{
    public function __construct(
        private OutputInterface $output,
        private ResultCacheKeyFactory $cacheKeyFactory,
        private ResultCacheStateFactory $cacheStateFactory,
    ) {
    }

    /**
     * @param string    $basePath
     * @param RuleSet[] $ruleSetList
     * @return ResultCacheEngine|null
     */
    public function create($basePath, CommandLineOptions $options, array $ruleSetList)
    {
        if (!$options->isCacheEnabled()) {
            $this->output->writeln('ResultCache is not enabled.', OutputInterface::VERBOSITY_VERY_VERBOSE);

            return null;
        }

        // create cache key based on the current rules and environment
        $cacheKey = $this->cacheKeyFactory->create($options->hasStrict(), $ruleSetList);

        // load result cache from file
        $state = $this->cacheStateFactory->fromFile($options->cacheFile());
        if ($state === null) {
            $this->output->writeln(
                'ResultCache is enabled, but no prior cache-result file exists.',
                OutputInterface::VERBOSITY_VERY_VERBOSE
            );
        }

        // the cache key doesn't match the stored cache key. Invalidate cache
        if ($state && !$state->getCacheKey()->isEqualTo($cacheKey)) {
            $this->output->writeln(
                'ResultCache is enabled, but the cache metadata doesn\'t match.',
                OutputInterface::VERBOSITY_VERY_VERBOSE
            );
            $state = null;
        } else {
            $this->output->writeln(
                'ResultCache is enabled, and read from ' . $options->cacheFile(),
                OutputInterface::VERBOSITY_VERY_VERBOSE
            );
        }

        return new ResultCacheEngine(
            new ResultCacheFileFilter($this->output, $basePath, $options->cacheStrategy(), $cacheKey, $state),
            new ResultCacheUpdater($this->output, $basePath),
            new ResultCacheWriter($options->cacheFile())
        );
    }
}
