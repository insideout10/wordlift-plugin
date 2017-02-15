#!/bin/bash

function travis_stop_sauce_connect() {
  if [[ ${_SC_PID} = unset ]] ; then
    echo "No running Sauce Connect tunnel found"
    return 1
  fi

  kill ${_SC_PID}

  for i in 0 1 2 3 4 5 6 7 8 9 ; do
    if kill -0 ${_SC_PID} &>/dev/null ; then
      echo "Waiting for graceful Sauce Connect shutdown"
      sleep 1
    else
      echo "Sauce Connect shutdown complete"
      return 0
    fi
  done

  if kill -0 ${_SC_PID} &>/dev/null ; then
    echo "Forcefully terminating Sauce Connect"
    kill -9 ${_SC_PID} &>/dev/null || true
  fi
}

travis_stop_sauce_connect
