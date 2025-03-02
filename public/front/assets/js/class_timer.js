class ExamTimer {
    constructor(e) {
        (this.startTime = Date.now()), (this.endTime = this.startTime + 6e4 * e);
    }
    getRemainingTime() {
        let e = Date.now();
        return Math.max(0, this.endTime - e);
    }
}
class TimerManager {
    constructor() {
        this.intervals = new Map();
    }
    setTimer(e, t, i) {
        this.clearTimer(e);
        let r = setInterval(t, i);
        return this.intervals.set(e, r), r;
    }
    clearTimer(e) {
        this.intervals.has(e) && (clearInterval(this.intervals.get(e)), this.intervals.delete(e));
    }
    clearAll() {
        for (let e of this.intervals.values()) clearInterval(e);
        this.intervals.clear();
    }
}
function debounce(e, t) {
    let i;
    return function (...r) {
        clearTimeout(i),
            (i = setTimeout(() => {
                e.apply(this, r);
            }, t));
    };
}
function calculateRemainingTime(startTime, timeRange, currentTime) {
    startTime = parseInt(startTime, 10);
    timeRange = parseInt(timeRange, 10);
    currentTime = parseInt(currentTime, 10);
    
    let elapsedTime = currentTime - startTime;
    
    let remainingTime = (timeRange * 60) - elapsedTime;
    
    return Math.max(0, remainingTime);
  }
